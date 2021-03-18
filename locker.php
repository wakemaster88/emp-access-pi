<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Locker Numpad</title>
    <link rel="stylesheet" href="css/main.css" />
  </head>
  <body>
	      <div style="text-align:center; padding:20px;">
		      <h1>Ticket scannen, oder Password eingeben!</h1><br>
			  <p>Ihr Schließfach öffnet sich automatisch nach dem Scan bzw. der Passwort Eingabe. Nach dem 1. Scan kann ein eigenes Passwort gewählt werden.</p>
	      </div>
    <div id="pinpad">
      <form >
        <input type="password" id="password" /></br>
        <input type="button" value="1" id="1" class="pinButton calc"/>
        <input type="button" value="2" id="2" class="pinButton calc"/>
        <input type="button" value="3" id="3" class="pinButton calc"/><br>
        <input type="button" value="4" id="4" class="pinButton calc"/>
        <input type="button" value="5" id="5" class="pinButton calc"/>
        <input type="button" value="6" id="6" class="pinButton calc"/><br>
        <input type="button" value="7" id="7" class="pinButton calc"/>
        <input type="button" value="8" id="8" class="pinButton calc"/>
        <input type="button" value="9" id="9" class="pinButton calc"/><br>
        <input type="button" value="clear" id="clear" class="pinButton clear"/>
        <input type="button" value="0" id="0 " class="pinButton calc"/>
        <input type="button" value="enter" id="enter" class="pinButton enter"/>
      </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="./js/app.js"></script>

  </body>
</html>
