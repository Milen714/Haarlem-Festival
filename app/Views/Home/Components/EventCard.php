<?php
namespace App\Views\Home\Components;
$reverseStyle = isset($isReverse) && $isReverse ? "flex-row-reverse" : "flex-row";
$borderClass = "";
$buttonStyle = "";
switch ($section->title)
{
    case str_contains($section->title, "History"):
        $borderClass = "border-[var(--home-history-accent)]";
        $buttonStyle = "home_history_button";
        break;
    case str_contains($section->title, "Yummy"):
        $borderClass = "border-[var(--home-yummy-accent)]";
        $buttonStyle = "home_yummy_button";
        break;
    case str_contains($section->title, "Jazz"):
        $borderClass = "border-[var(--home-jazz-accent)]";
        $buttonStyle = "home_jazz_button";
        break;
    case str_contains($section->title, "Dance"):
        $borderClass = "border-[var(--home-dance-accent)]";
        $buttonStyle = "home_dance_button";         
        break;
    case str_contains($section->title, "Magic"): 
        $borderClass = "border-[var(--home-magic-accent)]"; 
        $buttonStyle = "home_magic_button"; 
        break;
}

?>

<article class="flex <?php echo $reverseStyle ?> w-full justify-around items-center">

    <img class=" w-[40%] mr-5 h-auto object-cover rounded-xl border-4 <?php echo $borderClass ?> shadow-md"
        src="<?php echo $section->media->file_path ?>" alt="St. Bavo Church">

    <div class="flex flex-col w-[30%] items-center gap-4">
        <div class="flex flex-col items-center text-center">
            <?php echo $section->content_html ?>
        </div>
        <a class="<?php echo $buttonStyle ?>"
            href="<?php echo $section->cta_url ?>"><?php echo $section->cta_text ?></a>
    </div>
</article>