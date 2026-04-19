/**
 * Generate Report - Neer Nigrani
 * Opens a print-friendly report in a new window with all complaints data.
 */
function generateReport(tableSelector, title) {
    title = title || 'Complaints Report - Neer Nigrani';
    var table = document.querySelector(tableSelector);
    if (!table) {
        alert('No complaints data found to generate report.');
        return;
    }

    var rows = table.querySelectorAll('tr');
    var reportHTML = '<!DOCTYPE html><html><head><meta charset="UTF-8">';
    reportHTML += '<title>' + title + '</title>';
    reportHTML += '<style>';
    reportHTML += 'body{font-family:Arial,sans-serif;margin:30px;color:#333}';
    reportHTML += '.report-header{text-align:center;margin-bottom:30px;border-bottom:3px solid #2563eb;padding-bottom:15px}';
    reportHTML += '.report-header h1{margin:0;color:#1e40af;font-size:22px}';
    reportHTML += '.report-header p{margin:5px 0 0;color:#666;font-size:13px}';
    reportHTML += 'table{width:100%;border-collapse:collapse;margin-top:15px;font-size:13px}';
    reportHTML += 'th{background:#2563eb;color:#fff;padding:10px 8px;text-align:left;font-weight:600}';
    reportHTML += 'td{padding:9px 8px;border-bottom:1px solid #ddd}';
    reportHTML += 'tr:nth-child(even){background:#f8fafc}';
    reportHTML += '.report-footer{margin-top:25px;text-align:center;font-size:11px;color:#999;border-top:1px solid #ddd;padding-top:10px}';
    reportHTML += '@media print{body{margin:15px}button{display:none!important}}';
    reportHTML += '.print-btn{background:#2563eb;color:#fff;border:none;padding:10px 25px;border-radius:6px;cursor:pointer;font-size:14px;margin-bottom:20px}';
    reportHTML += '.print-btn:hover{background:#1d4ed8}';
    reportHTML += '</style></head><body>';
    reportHTML += '<div class="report-header">';
    reportHTML += '<h1>📋 ' + title + '</h1>';
    reportHTML += '<p>Generated on: ' + new Date().toLocaleString('en-IN') + '</p>';
    reportHTML += '</div>';
    reportHTML += '<button class="print-btn" onclick="window.print()">🖨️ Print Report</button>';
    reportHTML += '<table><thead><tr>';

    // Extract headers
    var headers = rows[0].querySelectorAll('th');
    for (var i = 0; i < headers.length; i++) {
        reportHTML += '<th>' + headers[i].innerText + '</th>';
    }
    reportHTML += '</tr></thead><tbody>';

    // Extract data rows
    for (var r = 1; r < rows.length; r++) {
        var cells = rows[r].querySelectorAll('td');
        if (cells.length === 0) continue;
        reportHTML += '<tr>';
        for (var c = 0; c < cells.length; c++) {
            reportHTML += '<td>' + cells[c].innerText + '</td>';
        }
        reportHTML += '</tr>';
    }

    reportHTML += '</tbody></table>';
    reportHTML += '<div class="report-footer">Neer Nigrani - Water Complaint Management System | Total Records: ' + (rows.length - 1) + '</div>';
    reportHTML += '</body></html>';

    var reportWindow = window.open('', '_blank');
    reportWindow.document.write(reportHTML);
    reportWindow.document.close();
}

/**
 * Admin report - extracts from complaint cards instead of table
 */
function generateAdminReport() {
    var cards = document.querySelectorAll('#complaints .card.mb-2');
    if (!cards || cards.length === 0) {
        alert('No complaints data found to generate report.');
        return;
    }

    var reportHTML = '<!DOCTYPE html><html><head><meta charset="UTF-8">';
    reportHTML += '<title>Admin Complaints Report - Neer Nigrani</title>';
    reportHTML += '<style>';
    reportHTML += 'body{font-family:Arial,sans-serif;margin:30px;color:#333}';
    reportHTML += '.report-header{text-align:center;margin-bottom:30px;border-bottom:3px solid #2563eb;padding-bottom:15px}';
    reportHTML += '.report-header h1{margin:0;color:#1e40af;font-size:22px}';
    reportHTML += '.report-header p{margin:5px 0 0;color:#666;font-size:13px}';
    reportHTML += 'table{width:100%;border-collapse:collapse;margin-top:15px;font-size:13px}';
    reportHTML += 'th{background:#2563eb;color:#fff;padding:10px 8px;text-align:left;font-weight:600}';
    reportHTML += 'td{padding:9px 8px;border-bottom:1px solid #ddd}';
    reportHTML += 'tr:nth-child(even){background:#f8fafc}';
    reportHTML += '.report-footer{margin-top:25px;text-align:center;font-size:11px;color:#999;border-top:1px solid #ddd;padding-top:10px}';
    reportHTML += '@media print{body{margin:15px}button{display:none!important}}';
    reportHTML += '.print-btn{background:#2563eb;color:#fff;border:none;padding:10px 25px;border-radius:6px;cursor:pointer;font-size:14px;margin-bottom:20px}';
    reportHTML += '.print-btn:hover{background:#1d4ed8}';
    reportHTML += '</style></head><body>';
    reportHTML += '<div class="report-header">';
    reportHTML += '<h1>📋 Admin Complaints Report - Neer Nigrani</h1>';
    reportHTML += '<p>Generated on: ' + new Date().toLocaleString('en-IN') + '</p>';
    reportHTML += '</div>';
    reportHTML += '<button class="print-btn" onclick="window.print()">🖨️ Print Report</button>';
    reportHTML += '<table><thead><tr>';
    reportHTML += '<th>Complaint ID</th><th>Name</th><th>Issue</th><th>Description</th><th>Area</th><th>District</th><th>Status</th><th>Date</th>';
    reportHTML += '</tr></thead><tbody>';

    cards.forEach(function(card) {
        var idEl = card.querySelector('.complaint-id');
        var badgeEl = card.querySelector('.badge');
        var h4El = card.querySelector('h4');
        var pEl = card.querySelector('p');
        var metaSpans = card.querySelectorAll('.complaint-meta span');

        var complaintId = idEl ? idEl.innerText.trim() : '';
        var status = badgeEl ? badgeEl.innerText.replace('●', '').trim() : '';
        var issue = h4El ? h4El.innerText.trim() : '';
        var desc = pEl ? pEl.innerText.trim() : '';
        var name = '', area = '', date = '';

        metaSpans.forEach(function(s) {
            var t = s.innerText.trim();
            if (t.startsWith('👤')) name = t.replace('👤', '').trim();
            else if (t.startsWith('📍')) area = t.replace('📍', '').trim();
            else if (t.startsWith('📅')) date = t.replace('📅', '').trim();
        });

        var parts = area.split(',');
        var areaName = parts[0] ? parts[0].trim() : '';
        var district = parts[1] ? parts[1].trim() : '';

        reportHTML += '<tr>';
        reportHTML += '<td>' + complaintId + '</td>';
        reportHTML += '<td>' + name + '</td>';
        reportHTML += '<td>' + issue + '</td>';
        reportHTML += '<td>' + desc + '</td>';
        reportHTML += '<td>' + areaName + '</td>';
        reportHTML += '<td>' + district + '</td>';
        reportHTML += '<td>' + status + '</td>';
        reportHTML += '<td>' + date + '</td>';
        reportHTML += '</tr>';
    });

    reportHTML += '</tbody></table>';
    reportHTML += '<div class="report-footer">Neer Nigrani - Water Complaint Management System | Total Records: ' + cards.length + '</div>';
    reportHTML += '</body></html>';

    var reportWindow = window.open('', '_blank');
    reportWindow.document.write(reportHTML);
    reportWindow.document.close();
}
