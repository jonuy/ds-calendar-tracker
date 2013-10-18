<script type="text/javascript">
var pageData = <?php echo json_encode($pageData); ?>
</script>

<h1>Select a Calendar to evaluate:</h1>

<ul>
<?php foreach($calendarIds as $id) { ?>
    <li><a href="individual_stats.php?id=<?php echo $id ?>"><?php echo $id ?></a></li>
<?php } ?>
</ul>