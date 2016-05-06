<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
include APPLICATION_PATH . '/application/views/common/weui/header.php';
?>

<link type="text/css" rel="stylesheet" href="/static/public/css/imgbox.css?v=20160505"/>
<link type="text/css" rel="stylesheet" href="/static/kendo/css/kendo.common.min.css?v=20160505"/>
<link type="text/css" rel="stylesheet" href="/static/kendo/css/kendo.default.min.css?v=20160505"/>

<div id="grid">
</div>
<script id="rowTemplate" type="text/x-kendo-tmpl">
    <tr data-uid="#: uid #">
        <td class="number">
           <span class="number">#: number #</span>
        </td>
        <td class="name">
           <span class="name">#: name #</span>
        </td>
        <td class="price1">
            <span class="price1">#: price1 #</span>
         </td>
        <td class="price2">
            <span class="price2">#: price2 #</span>
         </td>
       <td class="price3">
            <span class="price3">#: price3 #</span>
         </td>
        <td class="pic">
           <img src="#: pic #" onerror="http://placeholdit.imgix.net/~text?txtsize=14&txt=%E5%9B%BE%E8%A3%82%E4%BA%86&w=17&h=17&txttrack=0" />
        </td>
   </tr>
</script>

<script>
    var data = <?php echo json_encode($data['tbody']);?>;
    var columns = <?php $data['thead'][] = array('command'=>array('edit', 'destroy'), 'title'=>'操作', 'allowCopy'=>false);echo json_encode($data['thead']);?>;
</script>
<script type="text/javascript" src="/static/kendo/js/kendo.all.min.js"></script>

<script src="/static/public/js/jquery.imgbox.pack.js?d=20160110"></script>
<script src="/static/public/js/office/parseExcel.js?d=20160110"></script>
<?php include APPLICATION_PATH . '/application/views/common/weui/footer.php'; ?>