var App = angular.module('App', []);

App.factory('invoiceFactory', function () {

    return {
        statuses: [
            {id: 1, description: "Ожидает"},
            {id: 2, description: "Отправлен"},
            {id: 3, description: "Отменен"}
        ],
        invoices: [],
        editableInvoice: {}
    };
});

App.controller('InvoicesController', function PhoneListController($scope, $http, invoiceFactory) {

    /**
     * @param state string 'hide' | 'show'
     */
    $scope.modal = function (state) {
        $('#invoiceForm').modal(state);
    };

    $scope.changeForAllStatus = "DELETED";
    $scope.changeForAllStatuses = [{value : "DELETED", description : "удалить"}];

    $scope.checkAll = false;
    $scope.invoiceFactory = invoiceFactory;
    $scope.invoices = invoiceFactory.invoices;
    $scope.editableInvoice = $scope.invoiceFactory.editableInvoice;
    $scope.statuses = $scope.invoiceFactory.statuses;

    $scope.orderReverse = false;
    $scope.orderName = 'id';

    $scope.getStatus = function (status) {
        var statusKey = _.findKey($scope.statuses, {id: status});

        if ($scope.statuses[statusKey] !== undefined) {
            return $scope.statuses[statusKey].description;
        }

        return "Неизвестный статус";
    };

    $scope.setEditableInvoice = function (invoice) {
        $scope.editableInvoice = invoice;

        $scope.modal('show');
    };

    $scope.checkedAll = function() {
        angular.forEach($scope.invoices, function(invoice) {
            invoice.checked = $scope.checkAll;
        })
    };

    $scope.sortBy = function (orderName) {
        $scope.orderReverse = ($scope.orderName === orderName) ? !$scope.orderReverse : false;
        $scope.orderName = orderName;
    };

    $scope.create = function () {

        var invoice = {
            id: false,
            from: "",
            to: "",
            status: "",
            recipient: "",
            checked : false
        };

        $scope.setEditableInvoice(invoice);
    };


    $http({
        method: 'GET',
        url: '/invoices'
    }).then(function successCallback(response) {

        var invoices = response.data;
        angular.forEach(response.data, function(invoice) {
            invoice.checked = false;
            $scope.invoices.push(invoice);
        })
    });

    $scope.remove = function (ids) {

        $http({
            method: 'POST',
            url: '/invoices/delete',
            data :  {ids : ids},
        }).then(function successCallback(response) {

            console.log(response.data);
            angular.forEach(ids, function(value){

                var index = _.findKey($scope.invoices, {id: value});

                $scope.invoices.splice(index, 1);
            });
        });
    };

    $scope.edit = function (invoice) {
        $scope.setEditableInvoice(invoice);
    };


    $scope.save = function (invoice) {

        if (!invoice.id) {
            $http({
                method: 'POST',
                url: '/invoices',
                data: invoice
            }).then(function successCallback(response) {
                $scope.invoices.push(response.data.model);
                $scope.modal('hide');
            });
        } else {
            $http({
                method: 'PUT',
                url: '/invoices/' + invoice.id,
                data: invoice
            }).then(function successCallback(response) {
                $scope.modal('hide');
            });
        }
    };

    $scope.changeForAll = function() {

        if($scope.changeForAllStatus === "DELETED") {
            var ids = [];
            $scope.invoices.map(function(invoice) {
                if(invoice.checked) {
                    ids.push(invoice.id);
                }
            });

            $scope.remove(ids);
        }
    }
});