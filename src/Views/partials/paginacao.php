<?php if (isset($totalPaginas) && $totalPaginas > 1): ?>
<nav class="pagination">
    <?php if ($paginaAtual > 1): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>">Primeira</a>
    <?php endif; ?>

    <?php for ($i = $inicioPagina; $i <= $fimPagina; $i++): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
           class="<?= $i == $paginaAtual ? 'active' : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>

    <?php if ($paginaAtual < $totalPaginas): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $totalPaginas])) ?>">Última</a>
    <?php endif; ?>
</nav>
<?php endif; ?>
