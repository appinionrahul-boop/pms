require('./bootstrap');
import $ from 'jquery';
import 'datatables.net-dt/css/jquery.dataTables.css';
import 'datatables.net-dt';

$(document).ready(function () {
    $('#packagesTable').DataTable({
        pageLength: 10,
        ordering: true,
        searching: true,
        columnDefs: [
            { orderable: false, targets: 3 } // disable sorting on "Action"
        ]
    });
});
