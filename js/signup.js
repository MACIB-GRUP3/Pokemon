const message = document.querySelector("#errorMessage");
const form = document.querySelector("#signup-form");
const nombre = document.querySelector("#inputName");
const email = document.querySelector("#inputEmail");
const pwd = document.querySelector("#inputPwd");
const submit = document.querySelector("#submButton");
submit.disabled = true;

const enableButton = () => {
  if (
    email.value.length > 5 &&
    pwd.value.length >= 6 &&
    nombre.value.length > 0
  ) {
    submit.disabled = false;
  } else {
    submit.disabled = true;
  }
};



const validateEmail = (email) => {
  return /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(
    email
  );
};


const validatePwd = (pwd) => {
  return /[A-Z]/.test(pwd) && /[0-9]/.test(pwd) && pwd.length >= 6;
};

const signup = () => {
  if (
    validateEmail(email.value) &&
    validatePwd(pwd.value)
  ) {
    form.submit();
  } else {
    message.innerHTML = "Error: Please follow the instructions";
  }
};

submit.addEventListener("click", signup);

const verifySignUp = () => {
  if (localStorage.getItem("currentEmail")) {
    window.location.assign("main.php");
  }
};
