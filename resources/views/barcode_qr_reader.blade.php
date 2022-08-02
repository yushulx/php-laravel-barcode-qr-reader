<!DOCTYPE html>
<html>

<head>
    <title>PHP Laravel Barcode QR Reader</title>
    <meta name="_token" content="{{csrf_token()}}" />
</head>

<body>
    <H1>PHP Laravel Barcode QR Reader</H1>
    <form action="{{ route('image.upload') }}" method="post" enctype="multipart/form-data">
    @csrf
        Select barcode image:
        <input type="file" name="BarcodeQrImage" id="BarcodeQrImage" accept="image/*"><br>
        <input type="submit" value="Read Barcode" name="submit">
    </form>
    <img id="image" />
    <script>
        var input = document.querySelector('input[type=file]');
        input.onchange = function() {
            var file = input.files[0];
            var fileReader = new FileReader();
            fileReader.onload = function(e) {
                {
                    let image = document.getElementById('image');
                    image.src = e.target.result;
                }
            }
            fileReader.readAsDataURL(file);
        }
    </script>
</body>

</html>