
<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<section style="max-width: 480px; margin: 32px auto 48px auto; background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.10); padding: 36px 32px; font-family: 'Segoe UI', Arial, sans-serif;">
    <?php if (isset($_GET['success']) && $_GET['success'] === '1'): ?>
        <div style="background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px; padding: 10px 15px; margin-bottom: 18px; text-align:center; font-size: 1rem;">Regla laboral creada correctamente.</div>
    <?php endif; ?>
    <form method="post" action="/reglas-laborales/crear" autocomplete="off" style="display: flex; flex-direction: column; gap: 18px;">
        <div style="display: flex; flex-direction: column; gap: 6px;">
            <label for="horas_maximas" style="font-weight: 500; color: #333;">Horas máximas</label>
            <input type="number" id="horas_maximas" name="horas_maximas" required autocomplete="off" style="padding: 12px; border: 1px solid #cfd8dc; border-radius: 8px; font-size: 1.08rem; background: #f8fafc; transition: border 0.2s;">
        </div>
        <div style="display: flex; flex-direction: column; gap: 6px;">
            <label for="descanso_minimo" style="font-weight: 500; color: #333;">Descanso mínimo</label>
            <input type="number" id="descanso_minimo" name="descanso_minimo" required autocomplete="off" style="padding: 12px; border: 1px solid #cfd8dc; border-radius: 8px; font-size: 1.08rem; background: #f8fafc; transition: border 0.2s;">
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%; margin-top:10px; background-color:#005b96; color:white; font-size:1.25rem; font-weight:700; border:none; border-radius:8px; padding:14px 0; transition:background 0.2s; box-shadow:0 2px 8px rgba(0,91,150,0.08); letter-spacing:0.5px;">Guardar</button>
    </form>
</section>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>