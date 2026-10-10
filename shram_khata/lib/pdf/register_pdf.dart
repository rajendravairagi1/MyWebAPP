import 'dart:typed_data';

import 'package:pdf/pdf.dart';
import 'package:pdf/widgets.dart' as pw;

import '../core/dates.dart';
import '../core/money.dart';
import '../data/services/payment_service.dart';
import '../data/services/report_service.dart';
import '../domain/business_profile.dart';
import '../domain/calc.dart';
import 'pdf_kit.dart';

/// Branded management reports built from the same kit as statements/bills.
class RegisterPdf {
  RegisterPdf._();

  static Future<(pw.Document, PdfAssets, pw.ThemeData)> _start(BusinessProfile p, String title) async {
    Money.configureCode(p.currencyCode);
    final assets = await PdfAssets.load(p);
    final theme = await PdfKit.theme();
    return (pw.Document(theme: theme, title: title, author: p.name), assets, theme);
  }

  /// Attendance register: labour × day for a company or site over a period.
  static Future<Uint8List> attendance({
    required BusinessProfile profile,
    required String heading,
    required AttendanceMatrix matrix,
  }) async {
    final (doc, assets, theme) = await _start(profile, 'Attendance register');
    doc.addPage(pw.MultiPage(
      pageTheme: pw.PageTheme(
        pageFormat: PdfPageFormat.a4.landscape,
        margin: const pw.EdgeInsets.fromLTRB(28, 24, 28, 20),
        theme: theme,
      ),
      footer: (ctx) => PdfKit.footer(ctx, profile, assets),
      build: (ctx) {
        final from = D.parse(matrix.from);
        final to = D.parse(matrix.to);
        final out = <pw.Widget>[
          PdfKit.letterhead(profile, assets, title: 'ATTENDANCE REGISTER', meta: [
            ('Period', '${D.show(matrix.from)} – ${D.show(matrix.to)}'),
            ('Generated', D.showDt(DateTime.now())),
          ]),
          pw.SizedBox(height: 8),
          pw.Text(heading, style: PdfKit.t(12, bold: true, color: PdfKit.brandDark)),
          pw.SizedBox(height: 6),
        ];
        var month = DateTime(from.year, from.month);
        while (!month.isAfter(DateTime(to.year, to.month))) {
          final first = month.isBefore(from) ? from : month;
          final last = D.lastOfMonth(month).isAfter(to) ? to : D.lastOfMonth(month);
          out.add(pw.Text(D.monthTitle(month), style: PdfKit.t(10, bold: true)));
          out.add(pw.SizedBox(height: 3));
          out.add(_grid(matrix, D.range(first, last)));
          out.add(pw.SizedBox(height: 10));
          month = DateTime(month.year, month.month + 1);
        }
        out.add(pw.Text('P = full day   H = half day   A = absent   OT = overtime hours',
            style: PdfKit.t(8, color: PdfKit.muted)));
        return out;
      },
    ));
    return doc.save();
  }

  static pw.Widget _grid(AttendanceMatrix m, List<DateTime> days) {
    final widths = <int, pw.TableColumnWidth>{
      0: const pw.FixedColumnWidth(120),
      1: const pw.FixedColumnWidth(60),
      for (var i = 0; i < days.length; i++) i + 2: const pw.FixedColumnWidth(16),
      days.length + 2: const pw.FixedColumnWidth(32),
      days.length + 3: const pw.FixedColumnWidth(26),
    };
    pw.Widget c(String s, {bool bold = false, PdfColor? color, PdfColor? bg, pw.Alignment? a}) =>
        pw.Container(
          height: 17,
          color: bg,
          alignment: a ?? pw.Alignment.center,
          padding: const pw.EdgeInsets.symmetric(horizontal: 3),
          child: pw.Text(s, maxLines: 1, style: PdfKit.t(7.5, bold: bold, color: color)),
        );
    return pw.Table(
      columnWidths: widths,
      border: pw.TableBorder.all(color: PdfKit.line, width: 0.4),
      children: [
        pw.TableRow(
          decoration: pw.BoxDecoration(color: PdfKit.brand),
          children: [
            c('Labour', bold: true, color: PdfColors.white, a: pw.Alignment.centerLeft),
            c('Skill', bold: true, color: PdfColors.white, a: pw.Alignment.centerLeft),
            for (final d in days) c('${d.day}', bold: true, color: PdfColors.white),
            c('Days', bold: true, color: PdfColors.white),
            c('OT', bold: true, color: PdfColors.white),
          ],
        ),
        for (final r in m.rows)
          pw.TableRow(children: [
            c(r.labour.name, a: pw.Alignment.centerLeft),
            c(r.labour.skill, color: PdfKit.muted, a: pw.Alignment.centerLeft),
            for (final d in days)
              () {
                final cell = r.cells[D.ymd(d)];
                if (cell == null) {
                  return c('', bg: d.weekday == DateTime.sunday ? PdfKit.zebra : null);
                }
                return c(cell.letter,
                    bold: true,
                    color: PdfKit.statusColor(cell.letter),
                    bg: PdfKit.statusBg(cell.letter));
              }(),
            c(num1(days.fold(0.0, (a, d) => a + (r.cells[D.ymd(d)]?.value ?? 0))), bold: true),
            c(num1(days.fold(0.0, (a, d) => a + (r.cells[D.ymd(d)]?.ot ?? 0)))),
          ]),
      ],
    );
  }

  /// Every payment made in the period.
  static Future<Uint8List> payments({
    required BusinessProfile profile,
    required List<PaymentRow> rows,
    required String from,
    required String to,
  }) async {
    final (doc, assets, theme) = await _start(profile, 'Payment register');
    final right = pw.Alignment.centerRight;
    final cashOut = rows.where((r) => PayType.isCashOut(r.payment.type));
    final byMode = <String, int>{};
    for (final r in cashOut) {
      byMode[r.payment.mode] = (byMode[r.payment.mode] ?? 0) + r.payment.amount;
    }
    final total = cashOut.fold<int>(0, (a, r) => a + r.payment.amount);
    doc.addPage(pw.MultiPage(
      pageTheme: pw.PageTheme(
        pageFormat: PdfPageFormat.a4,
        margin: const pw.EdgeInsets.fromLTRB(30, 26, 30, 22),
        theme: theme,
      ),
      footer: (ctx) => PdfKit.footer(ctx, profile, assets),
      build: (ctx) => [
        PdfKit.letterhead(profile, assets, title: 'PAYMENT REGISTER', meta: [
          ('Period', '${D.show(from)} – ${D.show(to)}'),
          ('Generated', D.showDt(DateTime.now())),
        ]),
        pw.SizedBox(height: 10),
        pw.Row(children: [
          for (final m in PayMode.all)
            pw.Expanded(
              child: pw.Container(
                margin: const pw.EdgeInsets.only(right: 8),
                padding: const pw.EdgeInsets.all(8),
                decoration: pw.BoxDecoration(
                  color: PdfKit.brandTint,
                  borderRadius: pw.BorderRadius.circular(5),
                ),
                child: pw.Column(crossAxisAlignment: pw.CrossAxisAlignment.start, children: [
                  pw.Text(PayMode.label(m).toUpperCase(),
                      style: PdfKit.t(7.5, bold: true, color: PdfKit.muted, spacing: 0.8)),
                  pw.Text(Money.format(byMode[m] ?? 0), style: PdfKit.t(12, bold: true)),
                ]),
              ),
            ),
          pw.Expanded(
            child: pw.Container(
              padding: const pw.EdgeInsets.all(8),
              decoration: pw.BoxDecoration(
                color: PdfKit.brand,
                borderRadius: pw.BorderRadius.circular(5),
              ),
              child: pw.Column(crossAxisAlignment: pw.CrossAxisAlignment.start, children: [
                pw.Text('TOTAL PAID',
                    style: PdfKit.t(7.5, bold: true, color: PdfColors.white, spacing: 0.8)),
                pw.Text(Money.format(total),
                    style: PdfKit.t(12, bold: true, color: PdfColors.white)),
              ]),
            ),
          ),
        ]),
        pw.SizedBox(height: 12),
        pw.Table(
          columnWidths: {
            0: const pw.FixedColumnWidth(54),
            1: const pw.FlexColumnWidth(2.2),
            2: const pw.FixedColumnWidth(76),
            3: const pw.FixedColumnWidth(42),
            4: const pw.FlexColumnWidth(2),
            5: const pw.FixedColumnWidth(66),
          },
          children: [
            pw.TableRow(
              decoration: pw.BoxDecoration(color: PdfKit.brand),
              children: [
                PdfKit.headCell('Date'),
                PdfKit.headCell('Labour'),
                PdfKit.headCell('Type'),
                PdfKit.headCell('Mode'),
                PdfKit.headCell('Note'),
                PdfKit.headCell('Amount', align: right),
              ],
            ),
            for (var i = 0; i < rows.length; i++)
              pw.TableRow(
                decoration: pw.BoxDecoration(
                  color: i.isOdd ? PdfKit.zebra : PdfColors.white,
                  border: pw.Border(bottom: pw.BorderSide(color: PdfKit.line, width: 0.5)),
                ),
                children: [
                  PdfKit.cell(D.showShort(rows[i].payment.date), size: 8),
                  PdfKit.cell(rows[i].labourName, size: 8),
                  PdfKit.cell(PayType.label(rows[i].payment.type), size: 8),
                  PdfKit.cell(PayMode.label(rows[i].payment.mode), size: 8),
                  PdfKit.cell(
                      [rows[i].payment.note, rows[i].payment.reference]
                          .where((e) => e.isNotEmpty)
                          .join(' • '),
                      size: 8),
                  PdfKit.cell(Money.format(rows[i].payment.amount),
                      align: right, bold: true, size: 8),
                ],
              ),
          ],
        ),
      ],
    ));
    return doc.save();
  }

  /// Who is owed what (and who owes advances back) at a glance.
  static Future<Uint8List> balances({
    required BusinessProfile profile,
    required List<BalanceRow> rows,
    required String from,
    required String to,
  }) async {
    final (doc, assets, theme) = await _start(profile, 'Labour balances');
    final right = pw.Alignment.centerRight;
    final shown = rows.where((r) => r.ledger.earned != 0 || r.ledger.balance != 0).toList();
    final payable = shown.where((r) => r.ledger.balance > 0).fold<int>(0, (a, r) => a + r.ledger.balance);
    final recover = shown.where((r) => r.ledger.balance < 0).fold<int>(0, (a, r) => a - r.ledger.balance);
    doc.addPage(pw.MultiPage(
      pageTheme: pw.PageTheme(
        pageFormat: PdfPageFormat.a4,
        margin: const pw.EdgeInsets.fromLTRB(30, 26, 30, 22),
        theme: theme,
      ),
      footer: (ctx) => PdfKit.footer(ctx, profile, assets),
      build: (ctx) => [
        PdfKit.letterhead(profile, assets, title: 'LABOUR BALANCES', meta: [
          ('Period', '${D.show(from)} – ${D.show(to)}'),
          ('Generated', D.showDt(DateTime.now())),
        ]),
        pw.SizedBox(height: 10),
        pw.Row(children: [
          pw.Expanded(
            child: PdfKit.card('To pay labour', pw.Text(Money.format(payable), style: PdfKit.t(14, bold: true, color: PdfKit.absent))),
          ),
          pw.SizedBox(width: 10),
          pw.Expanded(
            child: PdfKit.card('Advances to recover', pw.Text(Money.format(recover), style: PdfKit.t(14, bold: true, color: PdfKit.half))),
          ),
        ]),
        pw.SizedBox(height: 12),
        pw.Table(
          columnWidths: {
            0: const pw.FlexColumnWidth(2.4),
            1: const pw.FixedColumnWidth(40),
            2: const pw.FixedColumnWidth(70),
            3: const pw.FixedColumnWidth(70),
            4: const pw.FixedColumnWidth(78),
          },
          children: [
            pw.TableRow(
              decoration: pw.BoxDecoration(color: PdfKit.brand),
              children: [
                PdfKit.headCell('Labour'),
                PdfKit.headCell('Days', align: right),
                PdfKit.headCell('Earned', align: right),
                PdfKit.headCell('Paid', align: right),
                PdfKit.headCell('Balance', align: right),
              ],
            ),
            for (var i = 0; i < shown.length; i++)
              pw.TableRow(
                decoration: pw.BoxDecoration(
                  color: i.isOdd ? PdfKit.zebra : PdfColors.white,
                  border: pw.Border(bottom: pw.BorderSide(color: PdfKit.line, width: 0.5)),
                ),
                children: [
                  PdfKit.cell('${shown[i].labour.name}  ·  ${shown[i].labour.skill}', size: 8.5),
                  PdfKit.cell(num1(shown[i].ledger.paidDays), align: right, size: 8.5),
                  PdfKit.cell(Money.format(shown[i].ledger.earned), align: right, size: 8.5),
                  PdfKit.cell(Money.format(shown[i].ledger.cashPaid + shown[i].ledger.deductions),
                      align: right, size: 8.5),
                  PdfKit.cell(
                    Money.format(shown[i].ledger.balance),
                    align: right,
                    bold: true,
                    size: 8.5,
                    color: shown[i].ledger.balance > 0
                        ? PdfKit.absent
                        : (shown[i].ledger.balance < 0 ? PdfKit.half : PdfKit.present),
                  ),
                ],
              ),
          ],
        ),
        pw.SizedBox(height: 6),
        pw.Text('Balance = opening + earned − paid − deductions. Red: agency owes the labourer. Amber: advance taken beyond earnings.',
            style: PdfKit.t(8, color: PdfKit.muted)),
      ],
    ));
    return doc.save();
  }
}
