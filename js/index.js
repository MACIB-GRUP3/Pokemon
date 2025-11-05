const verifyIndex = () => {
  if (localStorage.getItem("currentEmail")) {
    window.location.assign("main.php");
  }
};
