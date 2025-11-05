const form = document.querySelector("#signin-form");
const email = document.querySelector("#inputEmail");
const pwd = document.querySelector("#inputPwd");
const submit = document.querySelector("#submButton");
submit.disabled = true;

const enableButton = () => {
  if (email.value.length !== 0 && pwd.value.length >= 6) {
    submit.disabled = false;
  } else {
    submit.disabled = true;
  }
};

const signin = () => {
  form.submit();
};

submit.addEventListener("click", signin);

const verifySignIn = () => {
  if (document.cookie.includes("PHPSESSID")) {
    window.location.assign("main.php");
  }
};
