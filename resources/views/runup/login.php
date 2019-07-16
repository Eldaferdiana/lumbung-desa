
<!DOCTYPE HTML>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>RunupStudio - Creative and Passion</title>
  <script src="https://www.gstatic.com/firebasejs/5.8.6/firebase.js"></script>
  <script>
    // Initialize Firebase
    var config = {
      apiKey: "AIzaSyBi4qKy6yMUFlsxOAYZirhzzzMBJjO0C1Y",
      authDomain: "lumbung-desa-b1131.firebaseapp.com",
      databaseURL: "https://lumbung-desa-b1131.firebaseio.com",
      projectId: "lumbung-desa-b1131",
      storageBucket: "lumbung-desa-b1131.appspot.com",
      messagingSenderId: "1090195919721"
    };
    firebase.initializeApp(config);
  </script>
  <script>
    function smsLogin() {
      var phoneNumber = document.getElementById("phone_number").value;
      firebase.auth().signInWithPhoneNumber(phoneNumber)
        .then(function (confirmationResult) {
          // SMS sent. Prompt user to type the code from the message, then sign the
          // user in with confirmationResult.confirm(code).
          window.confirmationResult = confirmationResult;
        }).catch(function (error) {
          // Error; SMS not sent
          // ...
        });
    }
  </script>
</head>

<body>
  <form>
    <input value="+1" id="country_code" />
    <input placeholder="phone number" id="phone_number"/>
    <button onclick="smsLogin();">Login via SMS</button>
  </form>
</body>
