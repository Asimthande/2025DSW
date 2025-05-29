<!DOCTYPE html>
<html>
<head>
  <title>Google Sign-In</title>
  <meta name="google-signin-client_id" content="72387172929-9dmud5cuo2s1dimnch974rk96as6c583.apps.googleusercontent.com">
  <script src="https://accounts.google.com/gsi/client" async defer></script>
  <script>
    function handleCredentialResponse(response) {
      
      const xhr = new XMLHttpRequest();
      xhr.open("POST", "Goohle.php");
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onload = () => {
        alert("Response from server: " + xhr.responseText);
      };
      xhr.send("id_token=" + response.credential);
    }

    window.onload = function () {
      google.accounts.id.initialize({
        client_id: "72387172929-9dmud5cuo2s1dimnch974rk96as6c583.apps.googleusercontent.com",
        callback: handleCredentialResponse
      });
      google.accounts.id.renderButton(
        document.getElementById("buttonDiv"),
        { theme: "outline", size: "large" }
      );
    };
  </script>
</head>
<body>
  <h2>Sign in with Google</h2>
  <div id="buttonDiv"></div>
</body>
</html>