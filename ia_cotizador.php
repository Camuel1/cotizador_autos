
<!DOCTYPE html>
<html>
<head>
    <title>Simulación IA Cotizador</title>
</head>
<body>
    <h2>Simulación Inteligente de Cotización</h2>

    <form action="recomendar.php" method="POST">
        <!-- Pregunta 1 -->
        <label>¿Qué valoras más en un auto?</label><br>
        <input type="radio" name="preferencia" value="velocidad" required> 🚀 Velocidad<br>
        <input type="radio" name="preferencia" value="comodidad"> 👨‍👩‍👧 Comodidad<br>
        <input type="radio" name="preferencia" value="economia"> 💸 Economía<br><br>

        <!-- Pregunta 2 -->
        <label>¿Cómo prefieres pagar?</label><br>
        <input type="radio" name="pago" value="contado" required> Contado<br>
        <input type="radio" name="pago" value="24"> 24 cuotas<br>
        <input type="radio" name="pago" value="36"> 36 cuotas<br>
        <input type="radio" name="pago" value="48"> 48 cuotas<br><br>

        <!-- Pregunta 3 -->
        <label>¿Tienes una marca favorita?</label><br>
        <select name="marca">
            <option value="ninguna">No tengo preferencia</option>
            <option value="Toyota">Toyota</option>
            <option value="Peugeot">Peugeot</option>
            <option value="Audi">Audi</option>
            <option value="Subaru">Subaru</option>
        </select><br><br>

        <!-- Pregunta 4 -->
        <label>¿Prefieres caja manual o automática?</label><br>
        <input type="radio" name="caja" value="manual" required> Manual<br>
        <input type="radio" name="caja" value="automatica"> Automática<br><br>

        <!-- Pregunta 5 -->
        <label>¿Qué tamaño de motor prefieres?</label><br>
        <select name="motor">
            <option value="1.2">1.2</option>
            <option value="1.6">1.6</option>
            <option value="2.0">2.0</option>
            <option value="2.5">2.5</option>
            <option value="turbo">Turbo</option>
        </select><br><br>

        <!-- Pregunta 6 -->
        <label>¿Cuántas puertas prefieres?</label><br>
        <select name="puertas">
            <option value="2">2</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select><br><br>

        <input type="submit" value="Recomendar Auto">
    </form>
</body>
</html>
