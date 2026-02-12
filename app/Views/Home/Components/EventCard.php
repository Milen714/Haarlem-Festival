<?php
namespace App\Views\Home\Components;
$reverseStyle = isset($isReverse) && $isReverse ? "flex-row-reverse" : "flex-row";
$borderColor = "";
$buttonStyle = "";
switch ($section->title)
{
    case str_contains($section->title, "History"):
        $borderColor = "--home-history-accent";
        $buttonStyle = "home_history_button";
        break;
    case str_contains($section->title, "Yummy"):
        $borderColor = "--home-yummy-accent";
        $buttonStyle = "home_yummy_button";
        break;
    case str_contains($section->title, "Jazz"):
        $borderColor = "--home-jazz-accent";
        $buttonStyle = "home_jazz_button";
        break;
    case str_contains($section->title, "Dance"):
        $borderColor = "--home-dance-accent";
        $buttonStyle = "home_dance_button";         
        break;
    case str_contains($section->title, "Magic"): 
        $borderColor = "--home-magic-accent"; 
        $buttonStyle = "home_magic_button"; 
        break;
}

?>

<article class="flex <?php echo $reverseStyle ?> w-full justify-around items-center">

    <img class=" w-[40%] mr-5 h-auto object-cover rounded-xl border-4 border-[<?php echo $borderColor ?>] shadow-md"
        src="<?php echo $section->media->file_path ?>" alt="St. Bavo Church">

    <div class="flex flex-col w-[30%] items-center gap-4">
        <div class="flex flex-col items-center text-center">
            <?php echo $section->content_html ?>
        </div>
        <a class="<?php echo $buttonStyle ?>"
            href="<?php echo $section->cta_url ?>"><?php echo $section->cta_text ?></a>
    </div>
</article>