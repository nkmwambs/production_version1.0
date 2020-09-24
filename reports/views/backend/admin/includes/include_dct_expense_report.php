<table class='table table-striped'>
            <thead>
                <tr>
                    <th>FCP No.</th>
                    <th>Cluster Name</th>
                    <?php 
                        foreach(array_shift($data) as $header_key){
                    ?>
                        <th><?=$header_key;?></th>
                    <?php
                        }
                    ?>
                    <th>Total</th>
                </tr>
            </thead>
            <?php 
                //print_r($data);
            ?>
            <tbody>
               <?php 
                    foreach($data as $fcp_no => $array_of_values){
                ?>
                        <tr>
                            <td><?=$fcp_no;?></td>
                            <td><?=$array_of_values['cluster_name'];?></td>
                            <?php foreach($array_of_values['values'] as $array_of_value){?>
                                <td><?=number_format($array_of_value,2);?></td>
                            <?php }?>
                            <td></td>
                        </tr>
                <?php
                    }
               ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan='2'>Total</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>