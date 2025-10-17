<!DOCTYPE html>
<html>
<head>
    <title>Upload Receipt - Digital Warranty</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #ffffff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 450px;
            width: 100%;
        }
        h2 { color: #333; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; text-align: left; }
        label { font-weight: bold; color: #555; }
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        input[type="file"]::file-selector-button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #E85C50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        input[type="submit"]:hover { background-color: #c94d44; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Upload Receipt</h2>
       <form action="ocr/process_ocr.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="receipt_image">Choose receipt image:</label>
                <input type="file" name="receipt_image" id="receipt_image" required>
            </div>
            <input type="submit" value="Process Receipt">
        </form>
    </div>
</body>
</html>
