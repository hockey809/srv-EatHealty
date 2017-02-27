<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    
    <!-- Bootstrap Core CSS -->
    <link href="/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- MetisMenu CSS -->
    <link href="/bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
    <!-- Timeline CSS -->
    <link href="/dist/css/timeline.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/dist/css/sb-admin-2.css" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- DataTables CSS -->
    <link href="/bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet">
</head>
<body> 
<div id="page-wrapper">  
    <br/>
    <div>
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active"><a href="#subscriptions" aria-controls="subscriptions" role="tab" data-toggle="tab">{{ Lang::get('pages.customers.subscriptions') }}</a></li>
          <li role="presentation"><a onclick="tableVisitsInit()" href="#totalvisits" aria-controls="totalvisits" role="tab" data-toggle="tab">{{ Lang::get('pages.customers.visits') }}</a></li>
          <li role="presentation"><a onclick="tablePurchasesInit()" href="#purchases" aria-controls="purchases" role="tab" data-toggle="tab">{{ Lang::get('pages.customers.purchases') }}</a></li>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="subscriptions">
                <div class="table-container">
                    <table class="table table-bordered table-hover" id="subscriptions-list-table">
                        <thead>
                        <tr role="row" class="heading">
                            <th width="35%">{{ Lang::get('pages.customers.subscription_title') }}</th>
                            <th width="15%">{{ Lang::get('pages.subscriptions.status') }}</th>
                            <th>{{ Lang::get('pages.subscriptions.period')}}</th>
                            <th width="10%">{{ Lang::get('pages.customers.visits_short') }}</th>
                        </tr>
                        </thead> 
                        <tbody>
                            @foreach($data['active_subscriptions'] as $key => $subscription) 
                            <tr>
                                <td>{{ $subscription->title }}</td>
                                <td width="15%">
                                    <span style="color: {{$subscription->color}}">{{ $subscription->message }}</span>
                                        <span data-toggle="tooltip" 
                                              data-placement="top" 
                                              title="{{ $subscription->tooltip }}">
                                            <i class="fa fa-info-circle"></i>
                                        </span>
                                </td> 
                                @if($subscription->status == 'future') 
                                    <td><span style="color: {{$subscription->color}}"><b>{{App\TextFormat::fromDbToDisplayDate($subscription->start_time)}}</b></span> - {{ App\TextFormat::fromDbToDisplayDate($subscription->end_time)}}</td>
                                @else
                                    <td>{{App\TextFormat::fromDbToDisplayDate($subscription->start_time)}} - <span style="color: {{$subscription->color}}"><b>{{ App\TextFormat::fromDbToDisplayDate($subscription->end_time)}}</b></span></td>
                                @endif 
                                <td><a href="javascript:;" onclick="showSubscriptionVisits('{{ $subscription->id }}', '{{ $subscription->title }} - {{ $subscription->message }}')">{{ $subscription->current_visits }} / {{ $subscription->total_visits }}</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if(empty($data['active_subscriptions']))
                        <span id="noCurrentOrFuture" class="text-danger bg-danger">{{Lang::get('pages.dashboard.no_current_subs')}}</span><br/><br/>
                    @endif
                    <a class="btn btn-primary green" href="javascript:;" onclick="showSubscriptionHistory(this)">{{ trans('pages.customers.show_past_subscriptions') }} ({{ $data['totalsubscriptionscount'] }})</a>
                </div>
            </div>

            {{-- VISITS TAB CONTENT --}}
            <div role="tabpanel" class="tab-pane" id="totalvisits">
                <div class="table-container">
                    <table class="table table-bordered table-hover" id="visits-list-table">
                        <thead>
                        <tr role="row" class="heading">
                            <th>{{ Lang::get('pages.customers.service') }}</th>
                            <th>{{ Lang::get('pages.dashboard.site') }}</th>
                            <th>{{ Lang::get('common.date') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- PURCHASES TAB CONTENT --}}
            <div role="tabpanel" class="tab-pane" id="purchases">
                <div class="table-container">
                    <table class="table table-bordered table-hover" id="purchases-list-table">
                        <thead>
                        <tr role="row" class="heading">
                            <th width="50%">{{ Lang::get('pages.dashboard.title') }}</th>
                            <th width="14%">{{ Lang::get('pages.dashboard.total') }}</th>
                            <th width="26%">{{ Lang::get('common.date') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- SUBSCRIPTION VISITS MODAL --}}
@include('customers/partials/mobile_subscription_visits_modal')
    
{{-- PURCHASE DETAILS MODAL --}}
<div class="modal fade" id="fullPurchaseDetails" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="fullPurchaseDetailsBody" class="modal-body">
                <img src="/assets/global/img/loading-spinner-grey.gif" alt="" class="loading">
                <span>&nbsp;&nbsp;{{Lang::get('pages.profile.loading')}}...</span>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="/bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap Core JavaScript -->
<script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- DataTables JavaScript -->
<script src="/bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="/bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>

<script>
    $(document).ready(function(){
        $('body').tooltip({
            selector: '[data-toggle="tooltip"]'
        });
    });

    function showSubscriptionHistory(element) {
        $(element).replaceWith('<img src="/assets/global/img/loading-spinner-grey.gif" alt="" class="loading loading-cust-past-sub"><span class="loading-cust-past-sub">&nbsp;&nbsp;{{Lang::get("pages.dashboard.loading")}}...</span>');
        $('#noCurrentOrFuture').html('');
        $.ajax({
            type: 'POST',
            url: '/api/v1/customers/getPastSubscriptions/' + {{ $data['item']->id }} + '/0',
//            data: {'_token': '{{Session::getToken()}}' },
            success: function (data) {
                $("#subscriptions-list-table tbody").append(data);
                $('.loading-cust-past-sub').remove();
            },
            error: function (e) {
                console.log("Error getting past subscriptions: " + e.statusText);
            }
        });
    }

    function tablePurchasesInit() {
        if (!$.fn.dataTable.isDataTable('#purchases-list-table')) {
            initTable('purchases-list-table', '/api/v1/customers/getPurchases/' + {{ $data['item']->id }});
        }
    }

    function tableVisitsInit() {
        if (!$.fn.dataTable.isDataTable('#visits-list-table')) {
            initTable('visits-list-table', '/api/v1/customers/getVisits/' + {{ $data['item']->id }});
        }
    }

    function showSubscriptionVisits(customer_subscription_id, title) {
        $('#subscription-visits-title').text(title);
        $('#subscription-visits-modal').modal('show');
        if (!$.fn.dataTable.isDataTable('#subscription-visits-table')) {
            initTable('subscription-visits-table', '/api/v1/customers/getVisitsBySubscription/' + customer_subscription_id);
            $('#subscription-visits-table').css('width', '100%').dataTable().fnAdjustColumnSizing();
        }
        else {
            $('#subscription-visits-table').DataTable().ajax.url('/api/v1/customers/getVisitsBySubscription/' + customer_subscription_id).load();
        }
    }

    // Purchase details modal
    function showPurchaseDetails(batch) {
        $.ajax({
            url : '/api/v1/purchases/getFullPurchaseDetails/' + batch,
            method : 'get',
            cache: false,
            error : function() { $('#unvisitAlertMessage').show().delay(3000).fadeOut(); },
            success : function(response) {
                $("#fullPurchaseDetails").modal("show");
                $("#fullPurchaseDetailsBody").html(response);
            }
        });
    }
    
    function initTable(elId, listMethod, initComplete) {
        $("#" + elId).DataTable({
            loadingMessage: 'Loading...',
            bFilter: false,
            sDom: '<"top">rt<"bottom"p><"clear">',
            ordering: false,
            ajax: {
                'url': listMethod,
                'type': 'POST',
                'data': {'_token': '{{Session::getToken()}}' }
            },
            initComplete: function () {
                if (initComplete) initComplete();
            }
        });
    }
</script>
</body>
</html>