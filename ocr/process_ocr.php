<?php

require __DIR__ . '/../vendor/autoload.php';

use thiagoalessio\TesseractOCR\TesseractOCR;

session_start();

if (isset($_FILES['receipt_image']) && $_FILES['receipt_image']['error'] == 0) {
    $targetDir = __DIR__ . "/../uploads/"; 
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES['receipt_image']['name']);
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($_FILES['receipt_image']['tmp_name'], $targetFile)) {
        try {
            $text = (new TesseractOCR($targetFile))->run();
            $text_lower = strtolower($text); 

            // 1. Extract Product Name
            $productName = '';
            if (preg_match('/Product\s*Name:\s*(.+)/i', $text, $matches)) {
                $productName = trim($matches[1]);
            }

            // 2. Extract Brand
            $brand = '';
            if (preg_match('/Brand:\s*(.+)/i', $text, $matches)) {
                $brand = trim($matches[1]);
            }

            // 3. Extract Serial Number
            $serialNumber = '';
            if (preg_match('/Serial\s*number:\s*(\S+)/i', $text, $matches)) {
                $serialNumber = trim($matches[1]);
            }

            // 4. Extract Purchase Date
            $purchaseDate = '';
            if (preg_match('/Purchase\s*date:\s*(\d{2}[-\/]\d{2}[-\/]\d{2,4})/', $text, $matches)) {
                $purchaseDate = date('Y-m-d', strtotime(str_replace('/', '-', $matches[1])));
            }

            // 5. Extract Warranty Period and Unit
            $warrantyPeriod = '';
            $warrantyUnit = 'months';
            if (preg_match('/(\d+)\s*(year|years|yr|y|month|months|mo|mos)/i', $text, $matches)) {
                $warrantyPeriod = $matches[1];
                $unitKeyword = strtolower($matches[2]);
                if (strpos($unitKeyword, 'year') !== false || $unitKeyword == 'yr' || $unitKeyword == 'y') {
                    $warrantyUnit = 'years';
                } else if (strpos($unitKeyword, 'month') !== false || $unitKeyword == 'mo' || $unitKeyword == 'mos') {
                    $warrantyUnit = 'months';
                }
            }
            
            // 6. Categorize the product
            $category = 'Other';
            $categories = [
                'Electronics' => ['phone', 'mobile', 'laptop', 'computer', 'pc', 'tablet', 'speaker', 'tv', 'camera', 'samsung', 'apple', 'macbook'],
                'Appliances' => ['refrigerator', 'fridge', 'oven', 'microwave', 'washer', 'dryer', 'dishwasher', 'ac', 'cooler'],
                'Clothing' => ['shirt', 'jeans', 'dress', 't-shirt', 'jacket'],
                'Furniture' => ['table', 'chair', 'sofa', 'desk', 'bed'],
            ];
            foreach ($categories as $catName => $keywords) {
                foreach ($keywords as $keyword) {
                    if (strpos($text_lower, $keyword) !== false) {
                        $category = $catName;
                        break 2;
                    }
                }
            }

            $_SESSION['ocr_data'] = [
                "product_name" => $productName,
                "category" => $category,
                "brand" => $brand,
                "serial_number" => $serialNumber,
                "purchase_date" => $purchaseDate,
                "price" => "",
                "warranty_period" => $warrantyPeriod,
                "warranty_unit" => $warrantyUnit,
                "bill_file" => $fileName // Saving the filename
            ];

            header("Location: ../dashboard.php?page=add_product");
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = "OCR failed: " . $e->getMessage();
            header("Location: ../dashboard.php?page=add_product");
            exit;
        }
    } else {
        $_SESSION['error'] = "Error uploading the file.";
        header("Location: ../dashboard.php?page=add_product");
        exit;
    }
} else {
    $_SESSION['error'] = "Please upload a valid image.";
    header("Location: ../dashboard.php?page=add_product");
    exit;
}
?>
