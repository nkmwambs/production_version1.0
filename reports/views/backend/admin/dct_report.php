<hr>
<div class='row'>
    <div class='col-xs-12'>
        <?php 
            echo form_open('' , array('class' => 'form-horizontal form-groups-bordered validate'));
        ?>
            <div class='form-group'>
                <label class='col-xs-3'>Aggregation type</label>
                <div class='col-xs-9'> 
                    <select class='form-control'>
                        <option value=''>Select aggregation type</option>
                        <option value='1'>Amount Spent</option>
                        <option value='2'>Count of Beneficiary</option>
                        <option value='3'>Count of Caregivers</option>
                    </select>
                </div>
            </div>

            <div class='form-group'>
                <label class='col-xs-3'>Group data by</label>
                <div class='col-xs-9'> 
                    <select class='form-control'>
                        <option value=''>Select grouping</option>
                        <option value='1'>FCP By Fund</option>
                        <option value='2'>FCP By CIV</option>
                        <option value='3'>FCP By Support Mode</option>
                    </select>
                </div>
            </div>

            <div class='form-group'>
                <label class='col-xs-3'>Month</label>
                <div class='col-xs-9'> 
                    <input type='text' readonly='readonly' data-format='yyyy-mm-dd' class='form-control datepicker'/>
                </div>
            </div>

            <div class='form-group'>
                <div class='col-xs-12'>
                    <div class='btn btn-success'>Run</div>
                </div>
            </div>

        </form>
    </div>
</div>

<hr>

<div class='row'>
    <div class='col-xs-12'>
        <table class='table table-striped'>
            <thead>
                <tr>
                    <th>FCP No.</th>
                    <th>Cluster Name</th>
                    <th>R100</th>
                    <th>R200</th>
                    <th>R310</th>
                    <th>R330</th>
                    <th>R410</th>
                    <th>R425</th>
                    <th>R430</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>KE200</td>
                    <td>Ishiara</td>
                    <td>445</td>
                    <td>33</td>
                    <td>456</td>
                    <td>234</td>
                    <td>222</td>
                    <td>1244</td>
                    <td>233</td>
                    <td></td>
                </tr>

                <tr>
                    <td>KE567</td>
                    <td>Kakamega</td>
                    <td>55</td>
                    <td>4334</td>
                    <td>3322</td>
                    <td>222</td>
                    <td>554</td>
                    <td>346</td>
                    <td>7656</td>
                    <td></td>
                </tr>

                <tr>
                    <td>KE385</td>
                    <td>Kakamega</td>
                    <td>4322</td>
                    <td>33</td>
                    <td>455</td>
                    <td>654</td>
                    <td>4332</td>
                    <td>543</td>
                    <td>432</td>
                    <td></td>
                </tr>

                <tr>
                    <td>KE784</td>
                    <td>Machakos/ Isinya</td>
                    <td>322</td>
                    <td>4223</td>
                    <td>223</td>
                    <td>322</td>
                    <td>543</td>
                    <td>322</td>
                    <td>232</td>
                    <td></td>
                </tr>

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
                </tr>
            </tbody>
        </table>
    </div>
</div>