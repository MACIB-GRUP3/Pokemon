<?php 
require(__DIR__.'/php/mysqlSignUp.php');
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/svg" href="/img/pokedex.png" />
    <link
      rel="stylesheet"
      href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
      integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="/css/Navbar.css" />
    <link rel="stylesheet" href="/css/Form.css" />
    <title>Sign Up</title>
  </head>
  <body onload="verifySignUp()">
        <div class="Navbar">
          <div class="container-fluid">
            <a class="Navbar__brand" href="javascript:window.location.assign('index.html')">
              <img
                class="Navbar__brand-logo"
                src="/img/pokebad.svg"
                alt="Logo"
              />
              <span class="font-weight-light">Poké</span>
              <span class="font-weight-bold">dex</span>
              </div>
            </a> 
          </div>
        </div>
        <div class="Form__container">
          <div class="row">
            <div class="col-6">
              <div class="container-fluid">
                  <h1>
                    <span class="display-3 Home__title font-weight-light">Sign</span>
              <span class="display-3 Home__title font-weight-bold">Up</span>
                  </h1>
                 <form action="signup.php" method="post" id="signup-form">
                    <div class="form-group">
                      <label class="font-weight-light" for="inputEmail">
                        Email address</label>
                      <input onkeyup="enableButton()" type="email" name="email" class="form-control" id="inputEmail" aria-describedby="emailDescription" placeholder="Enter your email">
                      <small id="emailDescription" class="font-weight-bold">We'll never share your email with anyone else.</small>
                    </div>
                    <div class="form-group">
                      <label class="font-weight-light" for="inputName">
                        Name</label>
                      <input onkeyup="replaceNumbersAndSpecialChars()" type="text" name="name" class="form-control" id="inputName" placeholder="Enter your name">
                    </div>
                    <div class="form-group">
                      <label class="font-weight-light" for="inputPwd">Password</label>
                      <input onkeyup="enableButton()" minlength="6" type="password" name="pwd" class="form-control" id="inputPwd" aria-describedby="pwdDescription" placeholder="Enter your password">
                      <small id="pwdDescription" class="font-weight-bold">At least one capital letter, a number and a minimum length of 6 characters.</small>
                    </div>
                  </form>
                  <button type="button" id="submButton" class="font-weight-bold btn btn-primary">Register</button>
                  <small id="errorMessage" class="ml-3 font-weight-bold text-danger">
                    <?php 
                    echo $message;
                    ?>
                  </small>
              </div>
            </div>
            <div class="col-6">
              <img class="Form__img" src="/img/signup.png" alt="">
          </div>
          </div>
        </div>
        <script src="/js/signup.js"></script>
  </body>
</html>
