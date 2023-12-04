
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>LetsBuyAsia - Terms and Conditions</title>
    <link rel="shortcut icon" href="{{ asset('assets/img/logo.ico') }}" type="image/x-icon">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
      @media (max-width: 767px) {
        .container {
          padding-left: 7%;
          padding-right: 7%;
          padding-top: 8%;
          padding-bottom: 8%;
        }
      }

      @media (min-width: 768px) {
        .container {
          padding-top: 2%;
          padding-bottom: 2%;
        }
      }
    </style>
  </head>

  <body>
    <div class="container">
      <div class="row">
        <div class="col-md-8 offset-md-2">
          <h2>LetsBuyAsia - {{ $term_condition->title }}</h2>
          <hr>
          {!! $term_condition->content !!}
        </div>
      </div>
    </div>
  </body>
</html>
