<?php
namespace App\Views\Home\History;

?>

<section class="flex flex-col gap-6 bg_colors_home text_colors_home pt-4">
    <?php include __DIR__ . '/Components/HistoryHero.php'; ?>
    <?php include __DIR__ . '/Components/HistoryWelcome.php'; ?>
    <?php include __DIR__ . '/Components/HistoryMainLandmarks.php'; ?>
    <?php include __DIR__ . '/Components/HistoryBookTour.php'; ?>
</section>
 




  <!-- ⬇️ "Input file": estilos reutilizables (componentes/utilidades) 
  <style type="text/tailwindcss">
    @layer utilities {
      .h-hero { height: 52vh; }
      .overlay-hero { @apply bg-gradient-to-b from-black/60 via-black/40 to-transparent; }
    }
-->
