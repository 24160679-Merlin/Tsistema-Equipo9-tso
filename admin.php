<?php
// admin.php — Dashboard CRUD de Herramientas Truper
session_start();

// Proteger la página: si no está logueado, redirigir al login
if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
    header('Location: login.php');
    exit;
}

require 'db.php';

$mensaje = '';
$modo    = $_GET['modo'] ?? 'lista';   // lista | crear | editar
$id_edit = intval($_GET['id'] ?? 0);

// =============================================
// CREATE — Insertar nuevo registro
// =============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'crear') {
    $codigo      = $conn->real_escape_string($_POST['codigo']);
    $nombre      = $conn->real_escape_string($_POST['nombre']);
    $categoria   = $conn->real_escape_string($_POST['categoria']);
    $precio      = floatval($_POST['precio']);
    $stock       = intval($_POST['stock']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);

    $sql = "INSERT INTO herramientas (codigo, nombre, categoria, precio, stock, descripcion)
            VALUES ('$codigo','$nombre','$categoria',$precio,$stock,'$descripcion')";

    if ($conn->query($sql)) {
        $mensaje = '✅ Herramienta registrada correctamente.';
    } else {
        $mensaje = '❌ Error al registrar: ' . $conn->error;
    }
    $modo = 'lista';
}

// =============================================
// UPDATE — Actualizar registro
// =============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar') {
    $id          = intval($_POST['id']);
    $codigo      = $conn->real_escape_string($_POST['codigo']);
    $nombre      = $conn->real_escape_string($_POST['nombre']);
    $categoria   = $conn->real_escape_string($_POST['categoria']);
    $precio      = floatval($_POST['precio']);
    $stock       = intval($_POST['stock']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);

    $sql = "UPDATE herramientas
            SET codigo='$codigo', nombre='$nombre', categoria='$categoria',
                precio=$precio, stock=$stock, descripcion='$descripcion'
            WHERE id=$id";

    if ($conn->query($sql)) {
        $mensaje = '✅ Herramienta actualizada correctamente.';
    } else {
        $mensaje = '❌ Error al actualizar: ' . $conn->error;
    }
    $modo = 'lista';
}

// =============================================
// DELETE — Eliminar registro
// =============================================
if ($modo === 'eliminar' && $id_edit > 0) {
    $sql = "DELETE FROM herramientas WHERE id=$id_edit";
    if ($conn->query($sql)) {
        $mensaje = '✅ Herramienta eliminada correctamente.';
    } else {
        $mensaje = '❌ Error al eliminar: ' . $conn->error;
    }
    $modo = 'lista';
}

// =============================================
// READ — Obtener todos los registros
// =============================================
$result     = $conn->query("SELECT * FROM herramientas ORDER BY id ASC");
$herramientas = $result->fetch_all(MYSQLI_ASSOC);

// Obtener datos del registro a editar
$editData = [];
if ($modo === 'editar' && $id_edit > 0) {
    $r = $conn->query("SELECT * FROM herramientas WHERE id=$id_edit");
    $editData = $r->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Truper — Panel de Administración</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --am:#FFD100; --ne:#1a1a1a; --ro:#E31E24; --gr:#f5f5f5; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Barlow',sans-serif; background:var(--gr); color:var(--ne); }

        header {
            background:var(--ne);
            color:#fff;
            padding:0 40px;
            height:65px;
            display:flex;
            align-items:center;
            justify-content:space-between;
        }
        .logo { font-family:'Bebas Neue'; font-size:1.8rem; color:var(--am); letter-spacing:2px; }
        .header-right { display:flex; align-items:center; gap:20px; }
        .user-badge { color:#aaa; font-size:.85rem; }
        .btn-logout {
            background:var(--ro);
            color:#fff;
            padding:7px 18px;
            border-radius:4px;
            text-decoration:none;
            font-weight:700;
            font-size:.85rem;
        }

        main { max-width:1200px; margin:30px auto; padding:0 20px; }

        .page-title { font-family:'Bebas Neue'; font-size:2.2rem; margin-bottom:5px; }
        .page-sub { color:#777; margin-bottom:25px; }

        /* MENSAJE */
        .msg {
            padding:12px 20px;
            border-radius:5px;
            margin-bottom:20px;
            font-weight:600;
        }
        .msg.ok  { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
        .msg.err { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }

        /* BOTÓN NUEVO */
        .btn-nuevo {
            background:var(--am);
            color:var(--ne);
            padding:10px 24px;
            border-radius:5px;
            font-weight:700;
            text-decoration:none;
            display:inline-block;
            margin-bottom:20px;
        }
        .btn-nuevo:hover { background:#e6bc00; }

        /* TABLA */
        table { width:100%; border-collapse:collapse; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,.06); }
        thead { background:var(--ne); color:#fff; }
        thead th { padding:14px 12px; text-align:left; font-size:.85rem; text-transform:uppercase; letter-spacing:.5px; }
        tbody tr { border-bottom:1px solid #eee; }
        tbody tr:hover { background:#fffef0; }
        tbody td { padding:12px; font-size:.9rem; }
        .badge {
            background:var(--am);
            color:var(--ne);
            padding:3px 10px;
            border-radius:20px;
            font-size:.78rem;
            font-weight:700;
        }
        .btn-accion {
            padding:5px 12px;
            border-radius:4px;
            font-size:.8rem;
            font-weight:700;
            text-decoration:none;
            display:inline-block;
            margin-right:5px;
        }
        .btn-editar  { background:#007bff; color:#fff; }
        .btn-eliminar { background:var(--ro); color:#fff; }

        /* FORMULARIO */
        .form-card {
            background:#fff;
            border-radius:8px;
            padding:35px;
            box-shadow:0 2px 12px rgba(0,0,0,.06);
            max-width:700px;
        }
        .form-title { font-family:'Bebas Neue'; font-size:1.8rem; margin-bottom:20px; }
        .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:15px; }
        .form-group { display:flex; flex-direction:column; gap:5px; }
        .form-group.full { grid-column:1/-1; }
        label { font-weight:600; font-size:.88rem; color:#555; }
        input[type=text], input[type=number], select, textarea {
            padding:10px 12px;
            border:1px solid #ddd;
            border-radius:5px;
            font-family:'Barlow',sans-serif;
            font-size:.9rem;
        }
        input:focus, select:focus, textarea:focus { outline:none; border-color:var(--am); }
        textarea { resize:vertical; min-height:80px; }
        .btn-submit {
            background:var(--am);
            color:var(--ne);
            padding:11px 30px;
            border:none;
            border-radius:5px;
            font-weight:700;
            font-size:.95rem;
            cursor:pointer;
            margin-top:10px;
        }
        .btn-submit:hover { background:#e6bc00; }
        .btn-cancelar {
            background:#aaa;
            color:#fff;
            padding:11px 20px;
            border-radius:5px;
            text-decoration:none;
            font-weight:700;
            font-size:.95rem;
            margin-left:10px;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">⚙️ TRUPER — Panel Admin</div>
    <div class="header-right">
        <span class="user-badge">👤 <?= htmlspecialchars($_SESSION['usuario']) ?></span>
        <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
    </div>
</header>

<main>
    <h1 class="page-title">Gestión de Herramientas</h1>
    <p class="page-sub">Administra el inventario de herramientas Truper</p>

    <?php if ($mensaje): ?>
        <div class="msg <?= str_starts_with($mensaje,'✅') ? 'ok' : 'err' ?>">
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <!-- ===================== FORMULARIO CREAR ===================== -->
    <?php if ($modo === 'crear'): ?>
    <div class="form-card">
        <div class="form-title">➕ Nueva Herramienta</div>
        <form method="POST" action="admin.php">
            <input type="hidden" name="accion" value="crear">
            <div class="form-grid">
                <div class="form-group">
                    <label>Código</label>
                    <input type="text" name="codigo" placeholder="TRP-051" required>
                </div>
                <div class="form-group">
                    <label>Categoría</label>
                    <select name="categoria">
                        <option>Martillos</option><option>Desarmadores</option>
                        <option>Pinzas</option><option>Llaves</option>
                        <option>Medición</option><option>Sierras</option>
                        <option>Eléctricas</option><option>Seguridad</option>
                        <option>Jardín</option><option>Plomería</option>
                        <option>Electricidad</option><option>Accesorios</option>
                        <option>Acabados</option><option>Almacenaje</option>
                        <option>Pinturas</option><option>Corte</option>
                    </select>
                </div>
                <div class="form-group full">
                    <label>Nombre</label>
                    <input type="text" name="nombre" placeholder="Nombre de la herramienta" required>
                </div>
                <div class="form-group">
                    <label>Precio ($)</label>
                    <input type="number" name="precio" step="0.01" min="0" placeholder="0.00" required>
                </div>
                <div class="form-group">
                    <label>Stock</label>
                    <input type="number" name="stock" min="0" placeholder="0" required>
                </div>
                <div class="form-group full">
                    <label>Descripción</label>
                    <textarea name="descripcion" placeholder="Descripción breve del producto"></textarea>
                </div>
            </div>
            <button type="submit" class="btn-submit">💾 Guardar Herramienta</button>
            <a href="admin.php" class="btn-cancelar">Cancelar</a>
        </form>
    </div>

    <!-- ===================== FORMULARIO EDITAR ===================== -->
    <?php elseif ($modo === 'editar' && $editData): ?>
    <div class="form-card">
        <div class="form-title">✏️ Editar Herramienta</div>
        <form method="POST" action="admin.php">
            <input type="hidden" name="accion" value="editar">
            <input type="hidden" name="id" value="<?= $editData['id'] ?>">
            <div class="form-grid">
                <div class="form-group">
                    <label>Código</label>
                    <input type="text" name="codigo" value="<?= htmlspecialchars($editData['codigo']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Categoría</label>
                    <input type="text" name="categoria" value="<?= htmlspecialchars($editData['categoria']) ?>" required>
                </div>
                <div class="form-group full">
                    <label>Nombre</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($editData['nombre']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Precio ($)</label>
                    <input type="number" name="precio" step="0.01" value="<?= $editData['precio'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Stock</label>
                    <input type="number" name="stock" value="<?= $editData['stock'] ?>" required>
                </div>
                <div class="form-group full">
                    <label>Descripción</label>
                    <textarea name="descripcion"><?= htmlspecialchars($editData['descripcion']) ?></textarea>
                </div>
            </div>
            <button type="submit" class="btn-submit">💾 Actualizar</button>
            <a href="admin.php" class="btn-cancelar">Cancelar</a>
        </form>
    </div>

    <!-- ===================== TABLA DE REGISTROS ===================== -->
    <?php else: ?>
    <a href="admin.php?modo=crear" class="btn-nuevo">➕ Nueva Herramienta</a>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Código</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($herramientas as $h): ?>
            <tr>
                <td><?= $h['id'] ?></td>
                <td><strong><?= htmlspecialchars($h['codigo']) ?></strong></td>
                <td><?= htmlspecialchars($h['nombre']) ?></td>
                <td><span class="badge"><?= htmlspecialchars($h['categoria']) ?></span></td>
                <td>$<?= number_format($h['precio'],2) ?></td>
                <td><?= $h['stock'] ?></td>
                <td>
                    <a href="admin.php?modo=editar&id=<?= $h['id'] ?>" class="btn-accion btn-editar">✏️ Editar</a>
                    <a href="#"
                       onclick="if(confirm('¿Eliminar <?= htmlspecialchars(addslashes($h['nombre'])) ?>? Esta acción no se puede deshacer.')) window.location='admin.php?modo=eliminar&id=<?= $h['id'] ?>';"
                       class="btn-accion btn-eliminar">🗑️ Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</main>

</body>
</html>
