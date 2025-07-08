<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio - Sistema de Gestión de Incidencias</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #003366, #006699);
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            color: #fff;
        }

        .inicio-box {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 40px;
            max-width: 600px;
            width: 90%;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .inicio-box h1 {
            font-weight: 700;
            font-size: 2.5rem;
            color: #ffffff;
            margin-bottom: 10px;
        }

        .inicio-box p {
            font-size: 1.1rem;
            color: #e0e0e0;
        }

        .inicio-box img {
            width: 100px;
            margin: 20px 0;
        }

        .btn-acceso {
            margin-top: 30px;
            padding: 12px 30px;
            font-size: 1rem;
            font-weight: 600;
            background-color: #f9b115;
            color: #000;
            border: none;
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        .btn-acceso:hover {
            background-color: #f0a200;
            color: #000;
        }

        .creditos {
            margin-top: 40px;
            font-size: 0.9rem;
            color: #ccc;
        }
    </style>
</head>
<body>
    <div class="inicio-box">
        <h1>Bienvenido</h1>
        <p>Plataforma de gestión de incidencias en infraestructuras públicas</p>
        <img src="https://cdn-icons-png.flaticon.com/512/5988/5988227.png" alt="icono sistema" style="width: 90px;">

        <div>
            <a href="admin/index.php?path=auth/login" class="btn btn-acceso">Ingresar como Administrador</a>
        </div>

        <div class="creditos">
            © 2025 Sistema de Gestión de Incidencias. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>