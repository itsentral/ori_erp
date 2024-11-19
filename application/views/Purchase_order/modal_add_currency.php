<div class="box box-success">
    <table class="table table-bordered table-striped" id="my-grid3" width='100%'>
        <thead>
            <tr class='bg-blue'>
                <th class="text-center">#</th>
                <th class="text-center">Code</th> 
                <th class="text-center">Currency</th> 
                <th class="text-center">Country</th>
                <th class="text-center">#</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($currency as $key => $value) { $key++;
                echo "<tr>";
                    echo "<td align='center'>".$key."</td>";
                    echo "<td align='center'>".$value['kode']."</td>";
                    echo "<td>".$value['mata_uang']."</td>";
                    echo "<td>".$value['negara']."</td>";
                    echo "<td align='center'><input type='checkbox' name='currency[]' value='".$value['id']."'></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>
<?php
    echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-success','style'=>'min-width:100px; float:right; margin: 5px 0px 0px 0px;','value'=>'Create','content'=>'Save','id'=>'save_currency'));
?>
<br>