<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

echo '<h1>'.htmlspecialcharsbx($arParams["PAGE_TITLE"]).'</h1>';

if (!empty($arResult)) {
    echo '<table>';
    echo '<tr><th>ID</th><th>Название группы</th><th>Описание группы</th></tr>';
    foreach ($arResult as $group) {
        echo '<tr>';
        echo '<td>'.$group['ID'].'</td>';
        echo '<td>'.htmlspecialcharsbx($group['NAME']).'</td>';
        echo '<td>'.htmlspecialcharsbx($group['DESCRIPTION']).'</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo 'Группы не найдены.';
}
?>