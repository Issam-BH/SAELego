<?php ob_start(); ?>
<div class="container mt-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold text-primary"></i>Mes Tickets de Fidélité</h1>
        <p class="text-muted lead">Retrouvez ici vos codes promotionnels à utiliser lors de vos prochaines commandes.</p>
    </div>

    <div class="row justify-content-center g-4">
        <div class="col-md-5">
            <div class="card border-0 shadow-lg rounded-4 position-relative overflow-hidden h-100" style="background: linear-gradient(135deg, #FF9A9E 0%, #FECFEF 100%);">
                <div style="position: absolute; top: 50%; left: -15px; width: 30px; height: 30px; background: #f8f9fa; border-radius: 50%; transform: translateY(-50%);"></div>
                <div style="position: absolute; top: 50%; right: -15px; width: 30px; height: 30px; background: #f8f9fa; border-radius: 50%; transform: translateY(-50%);"></div>
                
                <div class="card-body p-5 text-center">
                    <h3 class="fw-bold text-white mb-2">Réduction de 10%</h3>
                    <p class="text-white mb-4" style="opacity: 0.9;">Valable sur toutes les dimensions de mosaïque.</p>
                    
                    <div class="bg-white rounded-3 py-3 px-4 d-inline-block shadow-sm border border-2 border-white border-dashed">
                        <span class="fs-3 fw-bold" style="letter-spacing: 3px; color: #FF9A9E; user-select: all;">LEGO10</span>
                    </div>
                    <p class="small text-white mt-3 mb-0"><i class="bi bi-info-circle me-1"></i>Copiez ce code dans le panier</p>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card border-0 shadow-lg rounded-4 position-relative overflow-hidden h-100" style="background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%);">
                <div style="position: absolute; top: 50%; left: -15px; width: 30px; height: 30px; background: #f8f9fa; border-radius: 50%; transform: translateY(-50%);"></div>
                <div style="position: absolute; top: 50%; right: -15px; width: 30px; height: 30px; background: #f8f9fa; border-radius: 50%; transform: translateY(-50%);"></div>
                
                <div class="card-body p-5 text-center">
                    <h3 class="fw-bold text-white mb-2">Réduction VIP 20%</h3>
                    <p class="text-white mb-4" style="opacity: 0.9;">Offre spéciale pour nos meilleurs créateurs.</p>
                    
                    <div class="bg-white rounded-3 py-3 px-4 d-inline-block shadow-sm border border-2 border-white border-dashed">
                        <span class="fs-3 fw-bold" style="letter-spacing: 3px; color: #a18cd1; user-select: all;">LEGO20</span>
                    </div>
                    <p class="small text-white mt-3 mb-0"><i class="bi bi-info-circle me-1"></i>Copiez ce code dans le panier</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5 text-center">
        <a href="index.php?page=home" class="btn btn-outline-secondary px-5 py-3 fw-bold rounded-pill shadow-sm">
            <i class="bi bi-arrow-left me-2"></i>Retourner créer une mosaïque
        </a>
    </div>
</div>
<?php $content = ob_get_clean(); require 'layout.php'; ?>