<?php
require('jwt_utils.php');
session_start();

var_dump(json_decode(jwt_decode($_SESSION['jwt']), true));

    // récuperer les informations de l'utilisateur depuis le token
   $username=json_decode(jwt_decode($_SESSION['jwt']), true)['username'];
   $IdUser=json_decode(jwt_decode($_SESSION['jwt']), true)['IdUser'];
   $IdRole=json_decode(jwt_decode($_SESSION['jwt']), true)['IdRole'];
    // $exp=json_decode(jwt_decode($_SESSION['token']), true)['exp'];
    echo ($username.$IdRole.$IdUser);
?>

<!DOCTYPE html>
<html>

<head>
  <title>My Chat App</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
  <header>
    <h1>My Chat App</h1>
    <nav>
      <ul>
        <li><a href="#">Home</a></li>
        <li><a href="#">Profile</a></li>
        <li><a href="#">Settings</a></li>
      </ul>
    </nav>
  </header>
  <main>
    <section class="post-form">
      <form>
        <label for="post">Create a new post:</label>
        <textarea id="post" name="post" rows="3"></textarea>
        <button type="submit">Post</button>
      </form>
    </section>
    <section class="post-list">
      <article>
        <header>
          <h2>John Doe</h2>
          <p>5 minutes ago</p>
        </header>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam sit amet placerat mi. Praesent eu neque tristique, blandit felis eu, tincidunt magna. Vestibulum bibendum tortor vitae lectus efficitur, a egestas lorem facilisis.</p>
        <footer>
          <button class="like">Like</button>
          <button class="dislike">Dislike</button>
          <span class="likes">0 likes</span>
        </footer>
      </article>
      <article>
        <header>
          <h2>Jane Doe</h2>
          <p>10 minutes ago</p>
        </header>
        <p>Suspendisse ultrices metus at augue venenatis, sit amet gravida ex aliquet. Morbi ornare vel lectus in lobortis. In lobortis felis eu pharetra lobortis. Nulla facilisi.</p>
        <footer>
          <button class="like">Like</button>
          <button class="dislike">Dislike</button>
          <span class="likes">0 likes</span>
        </footer>
      </article>
    </section>
  </main>
  <footer>
    <?php echo ($_SESSION['jwt']); ?>
    <p>&copy; 2023 My Chat App</p>
  </footer>
</body>

</html>