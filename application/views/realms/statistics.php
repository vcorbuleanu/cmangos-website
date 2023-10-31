<link rel="stylesheet" href="/public/css/realms.css">
<link rel="stylesheet" href="/public/css/_404.css">
<?
    if (count($realms) > 0) {
?>
<div class="section">
    <div class="header">Statistics</div>
    <div class="content">
        <?
            foreach ($realms as $realm) {
        ?>
        <div class="subheader">Race and Class statistics for our '<?=$realm['name']?>' realm:</div>
        <div class="graph races">
            <div class="data <?=$realm['version']?>">
        <?
                foreach ($realm['statistics']['available_races'] as $race) {
                        $count = 0;
                        if($realm['statistics']['races'][$race]['count'] !== 0 && $realm['statistics']['races'][$race]['total'] !== 0)
                            $count = 148*$realm['statistics']['races'][$race]['count']/$realm['statistics']['races'][$race]['total'];
        ?>
                <div class="bar" data-tooltip="<?=$realm['statistics']['races'][$race]['count'] . ' / ' . $realm['statistics']['races'][$race]['total'] . ' • ' . $count/148*100 . '%'?>">
                    <div class="fill" style="height: <?=$count?>px"></div>
                </div>
        <?
                }
        ?>
            </div>
            <div class="legend">
        <?
                foreach ($realm['statistics']['available_races'] as $race) {
        ?>
                <div class="race">
                    <img data-tooltip="<?=$realm['statistics']['races'][$race]['name']?>" src="/public/img/icons/race/<?=$race?>.webp"/>
                </div>
        <?
                }
        ?>
            </div>
        </div>
        <div class="graph classes">
            <div class="data <?=$realm['version']?>">
        <?
                foreach ($realm['statistics']['available_classes'] as $class) {
                        $count = 0;
                        if($realm['statistics']['classes'][$class]['count'] !== 0 && $realm['statistics']['classes'][$class]['total'] !== 0)
                            $count = 148*$realm['statistics']['classes'][$class]['count']/$realm['statistics']['classes'][$class]['total'];
        ?>
                <div class="bar" data-tooltip="<?=$realm['statistics']['classes'][$class]['count'] . ' / ' . $realm['statistics']['classes'][$class]['total'] . ' • ' . $count/148*100 . '%'?>">
                    <div class="fill" style="height: <?=$count?>px"></div>
                </div>
        <?
                }
        ?>
            </div>
            <div class="legend">
        <?
                foreach ($realm['statistics']['available_classes'] as $class) {
        ?>
                <div class="race">
                    <img data-tooltip="<?=$realm['statistics']['classes'][$class]['name']?>" src="/public/img/icons/class/<?=$class?>.webp"/>
                </div>
        <?
                }
        ?>
            </div>
        </div>
        <?
            }
        ?>
    </div>
</div>
<?
            } else {
?>
<div class="section _404">
    <div class="header">Statistics</div>
    <div class="content">
        <div class="warning">
            <p>“I couldn't find any realms in the database.”</p>
        </div>
        <div class="orc"></div>
    </div>
</div>
<?
            }
?>