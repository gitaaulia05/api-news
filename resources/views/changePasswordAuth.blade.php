<!DOCTYPE html>
<html lang="en">
<head>
  <title>Title of the document</title>
</head>
<body>

<h1>UBAH PASSWORD</h1>
<h5>Klik Tautan dibawah untuk ganti password</h5>
   {{-- ganti ke halaman frontend --}}
      {{-- pass ke frontend -> request ke api valid apa ga -> buka halaman frontend --}}
<a href="{{ url('http://127.0.0.1:9090/auth/ganti-password/' . $msg)}}"> Url Ganti Password </a>
<p style="color:red;">tidak untuk disebar luaskan !</p>
</body>
</html>