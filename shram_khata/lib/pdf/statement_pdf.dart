import 'dart:typed_data';

import 'package:pdf/pdf.dart';
import 'package:pdf/widgets.dart' as pw;

import '../core/dates.dart';
import '../core/money.dart';
import '../data/database.dart';
import '../data/services/report_service.dart';
import '../domain/calc.dart';
import 'pdf_kit.dart';

/// Labour statement: monthly attendance calendar (P / H / A), earnings,
/// payments and the closing balance.
class StatementPdf {
  StatementPdf._();

  static Future<Uint8List> build(StatementData d) async {
    Money.configureCode(d.profile.currencyCode);
    final assets = await PdfAssets.load(d.profile);
    final theme = await PdfKit.theme();
    final doc = pw.Document(
      theme: theme,
      title: 'Statement - ${d.labour.name}',
      author: d.profile.name,
    );
    final from = D.parse(d.from);
    final to = D.parse(d.to);

    final byDate = <String, List<AttendanceData>>{};
    for (final e in d.entries) {
      (byDate[e.date] ??= []).add(e);
    }

    // Months touched by the period.
    final months = <DateTime>[];
    var m = DateTime(from.year, from.month);
    while (!m.isAfter(DateTime(to.year, to.month))) {
      months.add(m);
      m = DateTime(m.year, m.month + 1);
    }

    final l = d.ledger;
    final sites = d.sitesWorked.toList();
    final qr = PdfKit.qrBlock(d.profile, assets, size: 44, compact: true);

    doc.addPage(pw.MultiPage(
      pageTheme: pw.PageTheme(
        pageFormat: PdfPageFormat.a4,
        margin: const pw.EdgeInsets.fromLTRB(30, 24, 30, 22),
        theme: theme,
      ),
      header: (ctx) => ctx.pageNumber == 1
          ? pw.SizedBox()
          : pw.Padding(
              padding: const pw.EdgeInsets.only(bottom: 8),
              child: pw.Text('${d.labour.name} • ${D.show(d.from)} to ${D.show(d.to)}',
                  style: PdfKit.t(8.5, color: PdfKit.muted)),
            ),
      footer: (ctx) => PdfKit.footer(ctx, d.profile, assets, qr: qr),
      build: (ctx) => [
        PdfKit.letterhead(
          d.profile,
          assets,
          title: 'LABOUR STATEMENT',
          meta: [
            ('Period', '${D.show(d.from)} – ${D.show(d.to)}'),
            ('Generated', D.showDt(DateTime.now())),
          ],
        ),
        pw.SizedBox(height: 10),
        _labourCard(d, sites),
        pw.SizedBox(height: 12),
        for (final month in months) ...[
          _calendar(month, from, to, byDate),
          pw.SizedBox(height: 8),
        ],
        _legend(),
        pw.SizedBox(height: 10),
        pw.Row(
          crossAxisAlignment: pw.CrossAxisAlignment.start,
          children: [
            pw.Expanded(child: _attendanceSummary(l)),
            pw.SizedBox(width: 12),
            pw.Expanded(child: _accountSummary(l)),
          ],
        ),
        pw.SizedBox(height: 10),
        _paymentsTable(d),
        pw.SizedBox(height: 14),
        pw.Row(
          mainAxisAlignment: pw.MainAxisAlignment.spaceBetween,
          children: [
            _signature('Labour signature / thumb'),
            _signature('Authorised signatory'),
          ],
        ),
      ],
    ));
    return doc.save();
  }

  static pw.Widget _labourCard(StatementData d, List<String> sites) {
    final lb = d.labour;
    final rate = d.rate;
    final rateText = d.ratePaise == 0
        ? '—'
        : d.payType == 'monthly'
            ? '${Money.format(d.ratePaise)} / month (${Money.format(rate!.dailyPaise)} / day)'
            : '${Money.format(d.ratePaise)} / day';
    return pw.Container(
      decoration: pw.BoxDecoration(
        color: PdfKit.brandTint,
        borderRadius: pw.BorderRadius.circular(6),
      ),
      padding: const pw.EdgeInsets.all(10),
      child: pw.Row(
        crossAxisAlignment: pw.CrossAxisAlignment.start,
        children: [
          pw.Expanded(
            flex: 3,
            child: pw.Column(
              crossAxisAlignment: pw.CrossAxisAlignment.start,
              children: [
                pw.Text(lb.name, style: PdfKit.t(14, bold: true, color: PdfKit.brandDark)),
                pw.SizedBox(height: 2),
                pw.Text(
                  [
                    lb.skill,
                    if (lb.fatherName.isNotEmpty) 'S/o ${lb.fatherName}',
                    if (lb.mobile.isNotEmpty) lb.mobile,
                  ].join('   •   '),
                  style: PdfKit.t(9, color: PdfKit.muted),
                ),
                if (sites.isNotEmpty) ...[
                  pw.SizedBox(height: 3),
                  pw.Text('Worked at: ${sites.join(';  ')}', style: PdfKit.t(8.5)),
                ],
              ],
            ),
          ),
          pw.SizedBox(width: 10),
          pw.Expanded(
            flex: 2,
            child: pw.Column(
              children: [
                PdfKit.kv('Pay rate', rateText, size: 8.5),
                if (rate != null)
                  PdfKit.kv('Overtime', '${Money.format(rate.otPerHourPaise)} / hour', size: 8.5),
                PdfKit.kv('Joined', D.show(lb.joinDate), size: 8.5),
                PdfKit.kv('Status', lb.status[0].toUpperCase() + lb.status.substring(1), size: 8.5),
              ],
            ),
          ),
        ],
      ),
    );
  }

  static pw.Widget _calendar(
    DateTime month,
    DateTime from,
    DateTime to,
    Map<String, List<AttendanceData>> byDate,
  ) {
    final first = DateTime(month.year, month.month, 1);
    final days = D.daysInMonth(month);
    final lead = first.weekday - 1; // Monday first
    final cells = <pw.Widget>[];
    for (var i = 0; i < lead; i++) {
      cells.add(pw.SizedBox());
    }
    for (var day = 1; day <= days; day++) {
      final date = DateTime(month.year, month.month, day);
      final inRange = !date.isBefore(from) && !date.isAfter(to);
      cells.add(_dayCell(date, inRange, byDate[D.ymd(date)]));
    }
    while (cells.length % 7 != 0) {
      cells.add(pw.SizedBox());
    }
    final rows = <pw.TableRow>[
      pw.TableRow(
        decoration: pw.BoxDecoration(color: PdfKit.brand),
        children: [
          for (final w in ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'])
            pw.Container(
              alignment: pw.Alignment.center,
              padding: const pw.EdgeInsets.symmetric(vertical: 4),
              child: pw.Text(w, style: PdfKit.t(7.5, bold: true, color: PdfColors.white)),
            ),
        ],
      ),
      for (var i = 0; i < cells.length; i += 7) pw.TableRow(children: cells.sublist(i, i + 7)),
    ];
    return pw.Column(
      crossAxisAlignment: pw.CrossAxisAlignment.start,
      children: [
        pw.Text(D.monthTitle(month), style: PdfKit.t(10.5, bold: true, color: PdfKit.brandDark)),
        pw.SizedBox(height: 4),
        pw.Table(
          border: pw.TableBorder.all(color: PdfKit.line, width: 0.6),
          children: rows,
        ),
      ],
    );
  }

  static pw.Widget _dayCell(DateTime date, bool inRange, List<AttendanceData>? entries) {
    final letters = entries == null ? <String>[] : entries.map((e) => e.status).toList();
    final value = entries?.fold<double>(0, (a, e) => a + dayValue(e.status)) ?? 0;
    final ot = entries?.fold<double>(
            0, (a, e) => a + (e.status == AttStatus.absent ? 0 : e.otHours)) ??
        0;
    String? letter;
    if (letters.isNotEmpty) {
      letter = value >= 1 ? 'P' : (value > 0 ? 'H' : 'A');
    }
    final isSunday = date.weekday == DateTime.sunday;
    final bg = !inRange
        ? PdfColors.grey100
        : letter != null
            ? PdfKit.statusBg(letter)
            : (isSunday ? PdfKit.zebra : PdfColors.white);
    return pw.Container(
      height: 22,
      color: bg,
      padding: const pw.EdgeInsets.fromLTRB(3, 2, 3, 2),
      child: pw.Stack(
        children: [
          pw.Positioned(
            left: 0,
            top: 0,
            child: pw.Text('${date.day}',
                style: PdfKit.t(6.5, color: inRange ? PdfKit.muted : PdfColors.grey500)),
          ),
          if (letter != null && inRange)
            pw.Center(
              child: pw.Column(
                mainAxisSize: pw.MainAxisSize.min,
                children: [
                  pw.Text(
                    letters.length > 1 && value >= 1 && letters.every((s) => s == 'H')
                        ? 'H+H'
                        : letter,
                    style: PdfKit.t(letter.length > 1 ? 9 : 11.5,
                        bold: true, color: PdfKit.statusColor(letter)),
                  ),
                  if (ot > 0)
                    pw.Text('+${num1(ot)}h', style: PdfKit.t(6.5, color: PdfKit.muted)),
                ],
              ),
            ),
        ],
      ),
    );
  }

  static pw.Widget _legend() {
    pw.Widget chip(String l, String text) => pw.Row(children: [
          pw.Container(
            width: 14,
            height: 14,
            alignment: pw.Alignment.center,
            decoration: pw.BoxDecoration(
              color: PdfKit.statusBg(l),
              borderRadius: pw.BorderRadius.circular(3),
            ),
            child: pw.Text(l, style: PdfKit.t(8, bold: true, color: PdfKit.statusColor(l))),
          ),
          pw.SizedBox(width: 4),
          pw.Text(text, style: PdfKit.t(8, color: PdfKit.muted)),
          pw.SizedBox(width: 14),
        ]);
    return pw.Row(children: [
      chip('P', 'Present (full day)'),
      chip('H', 'Half day'),
      chip('A', 'Absent'),
      pw.Text('+2h = overtime hours   Blank = not marked', style: PdfKit.t(8, color: PdfKit.muted)),
    ]);
  }

  static pw.Widget _attendanceSummary(Ledger l) {
    return PdfKit.card(
      'Attendance',
      pw.Column(children: [
        PdfKit.kv('Present (P)', _days(l.presentRows)),
        PdfKit.kv('Half day (H)', _days(l.halfRows)),
        PdfKit.kv('Absent (A)', _days(l.absentRows)),
        pw.Divider(color: PdfKit.line, thickness: 0.6, height: 8),
        PdfKit.kv('Paid days', num1(l.paidDays), bold: true),
        PdfKit.kv('Overtime', '${num1(l.otHours)} hours'),
      ]),
    );
  }

  static pw.Widget _accountSummary(Ledger l) {
    final bal = l.balance;
    final String caption;
    final PdfColor color;
    if (bal > 0) {
      caption = 'Payable to labour';
      color = PdfKit.absent;
    } else if (bal < 0) {
      caption = 'Advance to recover';
      color = PdfKit.half;
    } else {
      caption = 'Settled';
      color = PdfKit.present;
    }
    return PdfKit.card(
      'Account',
      pw.Column(children: [
        if (l.opening != 0) PdfKit.kv('Opening balance (b/f)', Money.format(l.opening)),
        PdfKit.kv('Wages for ${num1(l.paidDays)} days', Money.format(l.basePaise)),
        if (l.otPaise != 0) PdfKit.kv('Overtime', Money.format(l.otPaise)),
        PdfKit.kv('Total earned', Money.format(l.earned + l.opening), bold: true),
        pw.Divider(color: PdfKit.line, thickness: 0.6, height: 8),
        PdfKit.kv('Daily payments', '− ${Money.format(l.paidDaily)}'),
        if (l.paidAdvance != 0) PdfKit.kv('Advances', '− ${Money.format(l.paidAdvance)}'),
        if (l.paidSettlement != 0) PdfKit.kv('Settlements', '− ${Money.format(l.paidSettlement)}'),
        if (l.deductions != 0) PdfKit.kv('Deductions / fines', '− ${Money.format(l.deductions)}'),
        pw.SizedBox(height: 4),
        pw.Container(
          padding: const pw.EdgeInsets.symmetric(horizontal: 8, vertical: 6),
          decoration: pw.BoxDecoration(
            color: PdfKit.brandTint,
            borderRadius: pw.BorderRadius.circular(4),
          ),
          child: pw.Row(
            mainAxisAlignment: pw.MainAxisAlignment.spaceBetween,
            children: [
              pw.Text(caption, style: PdfKit.t(9, bold: true, color: color)),
              pw.Text(Money.format(bal.abs()), style: PdfKit.t(12, bold: true, color: color)),
            ],
          ),
        ),
      ]),
    );
  }

  static pw.Widget _paymentsTable(StatementData d) {
    if (d.payments.isEmpty) {
      return pw.Text('No payments in this period.', style: PdfKit.t(9, color: PdfKit.muted));
    }
    final total = d.payments
        .where((p) => PayType.isCashOut(p.type))
        .fold<int>(0, (a, p) => a + p.amount);
    final right = pw.Alignment.centerRight;
    return pw.Column(
      crossAxisAlignment: pw.CrossAxisAlignment.start,
      children: [
        pw.Text('Payment history', style: PdfKit.t(10.5, bold: true, color: PdfKit.brandDark)),
        pw.SizedBox(height: 4),
        pw.Table(
          columnWidths: {
            0: const pw.FixedColumnWidth(68),
            1: const pw.FixedColumnWidth(86),
            2: const pw.FixedColumnWidth(48),
            3: const pw.FlexColumnWidth(),
            4: const pw.FixedColumnWidth(76),
          },
          children: [
            pw.TableRow(
              decoration: pw.BoxDecoration(color: PdfKit.brand),
              children: [
                PdfKit.headCell('Date'),
                PdfKit.headCell('Type'),
                PdfKit.headCell('Mode'),
                PdfKit.headCell('Note / reference'),
                PdfKit.headCell('Amount', align: right),
              ],
            ),
            for (var i = 0; i < d.payments.length; i++)
              pw.TableRow(
                decoration: pw.BoxDecoration(
                  color: i.isOdd ? PdfKit.zebra : PdfColors.white,
                  border: pw.Border(bottom: pw.BorderSide(color: PdfKit.line, width: 0.5)),
                ),
                children: [
                  PdfKit.cell(D.showShort(d.payments[i].date)),
                  PdfKit.cell(PayType.label(d.payments[i].type)),
                  PdfKit.cell(d.payments[i].type == PayType.deduction
                      ? '—'
                      : PayMode.label(d.payments[i].mode)),
                  PdfKit.cell([d.payments[i].note, d.payments[i].reference]
                      .where((s) => s.isNotEmpty)
                      .join(' • ')),
                  PdfKit.cell(
                    (d.payments[i].type == PayType.deduction ? '− ' : '') +
                        Money.format(d.payments[i].amount),
                    align: right,
                    bold: true,
                  ),
                ],
              ),
            pw.TableRow(
              decoration: pw.BoxDecoration(color: PdfKit.brandTint),
              children: [
                PdfKit.cell(''),
                PdfKit.cell(''),
                PdfKit.cell(''),
                PdfKit.cell('Total paid', bold: true, align: right),
                PdfKit.cell(Money.format(total), bold: true, align: right),
              ],
            ),
          ],
        ),
      ],
    );
  }

  static String _days(int n) => '$n ${n == 1 ? 'day' : 'days'}';

  static pw.Widget _signature(String label) => pw.SizedBox(
        width: 150,
        child: pw.Column(children: [
          pw.Container(height: 0.8, color: PdfKit.muted),
          pw.SizedBox(height: 3),
          pw.Text(label, style: PdfKit.t(8, color: PdfKit.muted)),
        ]),
      );
}
