<?php
/* @var $this yii\web\View */

$this->registerJsFile('@web/js/app/main.js', ['position' => \yii\web\View::POS_END]);
?>

<div ng-controller="InvoicesController">

    <div class="form-group">
        <button ng-click="create()" class="btn btn-md btn-primary">добавить</button>
    </div>

    <table class="table table-striped">
        <tr>
            <th><input ng-model="checkAll" ng-click="checkedAll()" type="checkbox"/></th>
            <th class="sortable">Откуда <i ng-click="sortBy('from')" class="glyphicon glyphicon-resize-vertical"></i>
            </th>
            <th class="sortable">Куда <i ng-click="sortBy('to')" class="glyphicon glyphicon-resize-vertical"></i></th>
            <th class="sortable">Получатель <i ng-click="sortBy('recipient')"
                                               class="glyphicon glyphicon-resize-vertical"></i></th>
            <th class="sortable">Статус <i ng-click="sortBy('status')" class="glyphicon glyphicon-resize-vertical"></i>
            </th>
            <th class="sortable"></th>
        </tr>

        <tr ng-repeat="invoice in invoices | orderBy:orderName:orderReverse">

            <td><input ng-model="invoice.checked" type="checkbox"/></td>
            <td>{{invoice.from}}</td>
            <td>{{invoice.to}}</td>
            <td>{{invoice.recipient}}</td>
            <td>{{getStatus(invoice.status)}}</td>
            <td>
                <a ng-click="edit(invoice); setEditableInvoice(invoice)" href="#">изменить</a>
                <a ng-click="remove([invoice.id])">удалить</a>
            </td>
        </tr>
    </table>


    <div class="modal fade" id="invoiceForm">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">{{(editableInvoice.id) ? "Редактирование накладной" : "Добавление накладной"}}</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input placeholder="откуда" class="form-control" type='text' ng-model="editableInvoice.from"><br/>
                        <input placeholder="куда" class="form-control" type='text' ng-model="editableInvoice.to"><br/>
                        <input placeholder="получатель" class="form-control" type='text' ng-model="editableInvoice.recipient"><br/>

                        <select class="form-control" name="ngvalueselect" ng-model="editableInvoice.status">
                            <option ng-repeat="status in statuses" ng-value="status.id">{{status.description}}</option>
                        </select>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                    <button type="button" ng-click="save(editableInvoice)" class="btn btn-primary">Сохранить</button>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">

        <div class="col-sm-6">
            <select class="form-control" name="ngvalueselect" ng-model="changeForAllStatus">
                <option ng-repeat="status in changeForAllStatuses" ng-value="status.value">{{status.description}}
                </option>
            </select>
        </div>

        <div class="col-sm-4">
            <button ng-click="changeForAll()" class="btn btn-primary btn-sm">Применить ко всем</button>
        </div>
    </div>

</div>