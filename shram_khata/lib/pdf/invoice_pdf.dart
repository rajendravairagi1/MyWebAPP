import 'dart:typed_data';

import 'package:pdf/pdf.dart';
import 'package:pdf/widgets.dart' as pw;

import '../core/dates.dart';
import '../core/money.dart';
import '../data/services/report_service.dart';
import 'pdf_kit.dart';

/// Company bill: letterhead, parties, itemised lines, tax, totals, bank/QR
/// and an attendance annexure so the company can verify every day billed.
class InvoicePdf {
  InvoicePdf._();

  static const _rowPad = pw.EdgeInsets.symmetric(horizontal: 6, vertical: 3.6);

  static Future<Uint8List> build(InvoiceBundle b) async {
    final p = b.profile;
    final inv = b.detail.invoice;
    Money.configureCode(p.currencyCode);
    final assets = await PdfAssets.load(p);
    final theme = await PdfKit.theme();
    final doc = pw.Document(theme: theme, title: 'Invoice ${inv.number}', author: p.name);

    final taxInvoice = p.taxEnabled && p.taxId.trim().isNotEmpty && inv.taxRate > 0;
    final title = taxInvoice ? 'TAX INVOICE' : 'INVOICE';
    final cancelled = inv.status == 'cancelled';
    final outstanding = b.detail.outstanding;
    final qr = PdfKit.qrBlock(p, assets,
        amountPaise: outstanding > 0 ? outstanding : null, note: inv.number, size: 74);

    final pageTheme = pw.PageTheme(
      pageFormat: PdfPageFormat.a4,
      margin: const pw.EdgeInsets.fromLTRB(30, 26, 30, 22),
      theme: theme,
      buildBackground: cancelled
          ? (ctx) => pw.FullPage(
                ignoreMargins: true,
                child: pw.Center(
                  child: pw.Transform.rotate(
                    angle: 0.6,
                    child: pw.Text('CANCELLED',
                        style: PdfKit.t(80, bold: true, color: PdfColor.fromInt(0x22B91C1C))),
                  ),
                ),
              )
          : null,
    );

    doc.addPage(pw.MultiPage(
      pageTheme: pageTheme,
      footer: (ctx) => PdfKit.footer(ctx, p, assets),
      build: (ctx) => [
        PdfKit.letterhead(
          p,
          assets,
          title: title,
          branchLine: b.branch != null && b.branch!.name != 'Main' ? b.branch!.name : null,
          meta: [
            ('Invoice no.', inv.number),
            ('Date', D.show(inv.issueDate)),
            ('Due date', D.show(inv.dueDate)),
            ('Period', '${D.showShort(inv.periodFrom)} – ${D.show(inv.periodTo)}'),
          ],
        ),
        pw.SizedBox(height: 12),
        _parties(b),
        pw.SizedBox(height: 12),
        _linesTable(b),
        pw.SizedBox(height: 10),
        _totals(b),
        pw.SizedBox(height: 14),
        pw.Row(
          crossAxisAlignment: pw.CrossAxisAlignment.start,
          children: [
            pw.Expanded(child: _paymentDetails(b)),
            if (qr != null) ...[pw.SizedBox(width: 14), qr],
          ],
        ),
        // One unbreakable block so notes, terms and signatures stay together.
        pw.Container(
          child: pw.Column(
            crossAxisAlignment: pw.CrossAxisAlignment.start,
            children: [
              if (inv.notes.trim().isNotEmpty || p.invoiceTerms.trim().isNotEmpty) ...[
                pw.SizedBox(height: 10),
                _terms(b),
              ],
              pw.SizedBox(height: 16),
              pw.Row(
                mainAxisAlignment: pw.MainAxisAlignment.spaceBetween,
                children: [
                  _sign('Receiver\'s signature & stamp'),
                  _sign('For ${p.name.isEmpty ? 'the agency' : p.name}'),
                ],
              ),
            ],
          ),
        ),
        if (b.matrix.rows.isNotEmpty) ...[
          pw.SizedBox(height: 18),
          pw.Text('Attendance annexure — ${inv.number}',
              style: PdfKit.t(13, bold: true, color: PdfKit.brandDark)),
          pw.SizedBox(height: 2),
          pw.Text(
            '${b.detail.company.name}${b.detail.site != null ? ' • ${b.detail.site!.name}' : ''}   |   ${D.show(inv.periodFrom)} – ${D.show(inv.periodTo)}',
            style: PdfKit.t(9, color: PdfKit.muted),
          ),
          pw.SizedBox(height: 8),
          ..._matrices(b.matrix),
          pw.SizedBox(height: 4),
          pw.Text('P = full day   H = half day   A = absent   Blank = not on duty',
              style: PdfKit.t(8, color: PdfKit.muted)),
        ],
      ],
    ));
    return doc.save();
  }

  static pw.Widget _parties(InvoiceBundle b) {
    final c = b.detail.company;
    final site = b.detail.site;
    return pw.Row(
      crossAxisAlignment: pw.CrossAxisAlignment.start,
      children: [
        pw.Expanded(
          child: PdfKit.card(
            'Billed to',
            pw.Column(
              crossAxisAlignment: pw.CrossAxisAlignment.start,
              children: [
                pw.Text(c.name, style: PdfKit.t(11.5, bold: true)),
                if (c.address.isNotEmpty)
                  pw.Padding(
                    padding: const pw.EdgeInsets.only(top: 2),
                    child: pw.Text(c.address, style: PdfKit.t(8.5)),
                  ),
                if (c.gstin.isNotEmpty)
                  pw.Padding(
                    padding: const pw.EdgeInsets.only(top: 2),
                    child: pw.Text('${b.profile.taxLabel}: ${c.gstin}', style: PdfKit.t(8.5, bold: true)),
                  ),
                if (c.contactPerson.isNotEmpty || c.mobile.isNotEmpty)
                  pw.Padding(
                    padding: const pw.EdgeInsets.only(top: 2),
                    child: pw.Text(
                      [c.contactPerson, c.mobile].where((e) => e.isNotEmpty).join('  •  '),
                      style: PdfKit.t(8.5, color: PdfKit.muted),
                    ),
                  ),
              ],
            ),
          ),
        ),
        pw.SizedBox(width: 10),
        pw.Expanded(
          child: PdfKit.card(
            'Service details',
            pw.Column(
              children: [
                PdfKit.kv('Service', 'Manpower supply', size: 8.5),
                if (site != null) PdfKit.kv('Site', site.name, size: 8.5),
                PdfKit.kv('Period', '${D.showShort(b.detail.invoice.periodFrom)} – ${D.show(b.detail.invoice.periodTo)}', size: 8.5),
                PdfKit.kv('Total man-days', num1(b.detail.lines.fold(0.0, (a, l) => a + l.days)), size: 8.5),
              ],
            ),
          ),
        ),
      ],
    );
  }

  static pw.Widget _linesTable(InvoiceBundle b) {
    final lines = b.detail.lines;
    final hasOt = lines.any((l) => l.otHours > 0);
    final right = pw.Alignment.centerRight;
    final widths = <int, pw.TableColumnWidth>{
      0: const pw.FixedColumnWidth(24),
      1: const pw.FlexColumnWidth(3),
      2: const pw.FlexColumnWidth(1.6),
      3: const pw.FixedColumnWidth(40),
      4: const pw.FixedColumnWidth(58),
      if (hasOt) 5: const pw.FixedColumnWidth(60),
      (hasOt ? 6 : 5): const pw.FixedColumnWidth(72),
    };
    return pw.Table(
      columnWidths: widths,
      children: [
        pw.TableRow(
          decoration: pw.BoxDecoration(color: PdfKit.brand),
          children: [
            PdfKit.headCell('#'),
            PdfKit.headCell('Labour'),
            PdfKit.headCell('Skill'),
            PdfKit.headCell('Days', align: right),
            PdfKit.headCell('Rate / day', align: right),
            if (hasOt) PdfKit.headCell('Overtime', align: right),
            PdfKit.headCell('Amount', align: right),
          ],
        ),
        for (var i = 0; i < lines.length; i++)
          pw.TableRow(
            decoration: pw.BoxDecoration(
              color: i.isOdd ? PdfKit.zebra : PdfColors.white,
              border: pw.Border(bottom: pw.BorderSide(color: PdfKit.line, width: 0.5)),
            ),
            children: [
              PdfKit.cell('${i + 1}', color: PdfKit.muted, padding: _rowPad),
              PdfKit.cell(lines[i].description, padding: _rowPad),
              PdfKit.cell(lines[i].skill, color: PdfKit.muted, padding: _rowPad),
              PdfKit.cell(num1(lines[i].days), align: right, padding: _rowPad),
              PdfKit.cell(Money.format(lines[i].rate), align: right, padding: _rowPad),
              if (hasOt)
                PdfKit.cell(
                  lines[i].otHours > 0
                      ? '${num1(lines[i].otHours)}h × ${Money.format(lines[i].otRate)}'
                      : '—',
                  align: right,
                  size: 8,
                  padding: _rowPad,
                ),
              PdfKit.cell(Money.format(lines[i].amount), align: right, bold: true, padding: _rowPad),
            ],
          ),
      ],
    );
  }

  static pw.Widget _totals(InvoiceBundle b) {
    final inv = b.detail.invoice;
    final paid = b.detail.paid;
    return pw.Row(
      crossAxisAlignment: pw.CrossAxisAlignment.start,
      children: [
        pw.Expanded(
          child: pw.Padding(
            padding: const pw.EdgeInsets.only(right: 14, top: 2),
            child: pw.Column(
              crossAxisAlignment: pw.CrossAxisAlignment.start,
              children: [
                pw.Text('AMOUNT IN WORDS',
                    style: PdfKit.t(7.5, bold: true, color: PdfKit.muted, spacing: 0.8)),
                pw.SizedBox(height: 3),
                pw.Text(Money.inWords(inv.total), style: PdfKit.t(9, bold: true)),
              ],
            ),
          ),
        ),
        pw.SizedBox(
          width: 215,
          child: pw.Column(
            children: [
              PdfKit.kv('Subtotal', Money.format(inv.subtotal)),
              if (inv.taxAmount > 0)
                PdfKit.kv('${inv.taxLabel} @ ${num1(inv.taxRate)}%', Money.format(inv.taxAmount)),
              pw.SizedBox(height: 3),
              pw.Container(
                padding: const pw.EdgeInsets.symmetric(horizontal: 8, vertical: 7),
                decoration: pw.BoxDecoration(
                  color: PdfKit.brand,
                  borderRadius: pw.BorderRadius.circular(4),
                ),
                child: pw.Row(
                  mainAxisAlignment: pw.MainAxisAlignment.spaceBetween,
                  children: [
                    pw.Text('Total', style: PdfKit.t(10, bold: true, color: PdfColors.white)),
                    pw.Text(Money.format(inv.total),
                        style: PdfKit.t(13, bold: true, color: PdfColors.white)),
                  ],
                ),
              ),
              if (paid > 0) ...[
                pw.SizedBox(height: 4),
                PdfKit.kv('Received', '− ${Money.format(paid)}'),
                PdfKit.kv('Balance due', Money.format(b.detail.outstanding),
                    bold: true, color: PdfKit.absent),
              ],
            ],
          ),
        ),
      ],
    );
  }

  static pw.Widget _paymentDetails(InvoiceBundle b) {
    final p = b.profile;
    if (!p.hasBank && !p.hasUpi) {
      return pw.Text('Payment terms: ${b.profile.paymentTermsDays} days from invoice date.',
          style: PdfKit.t(8.5, color: PdfKit.muted));
    }
    return PdfKit.card(
      'Payment details',
      pw.Column(
        children: [
          if (p.hasBank) ...[
            PdfKit.kv('Account name', p.accountName.isEmpty ? p.name : p.accountName, size: 8.5),
            PdfKit.kv('Bank', p.bankName, size: 8.5),
            PdfKit.kv('Account no.', p.accountNumber, size: 8.5),
            PdfKit.kv('IFSC', p.ifsc, size: 8.5),
          ],
          if (p.hasUpi) PdfKit.kv('UPI', p.upiId, size: 8.5),
        ],
      ),
    );
  }

  static pw.Widget _terms(InvoiceBundle b) {
    final notes = b.detail.invoice.notes.trim();
    final terms = b.profile.invoiceTerms.trim();
    return pw.Column(
      crossAxisAlignment: pw.CrossAxisAlignment.start,
      children: [
        if (notes.isNotEmpty) ...[
          pw.Text('NOTES', style: PdfKit.t(7.5, bold: true, color: PdfKit.muted, spacing: 0.8)),
          pw.SizedBox(height: 2),
          pw.Text(notes, style: PdfKit.t(8.5)),
          pw.SizedBox(height: 6),
        ],
        if (terms.isNotEmpty) ...[
          pw.Text('TERMS & CONDITIONS',
              style: PdfKit.t(7.5, bold: true, color: PdfKit.muted, spacing: 0.8)),
          pw.SizedBox(height: 2),
          pw.Text(terms, style: PdfKit.t(8.5, color: PdfKit.muted)),
        ],
      ],
    );
  }

  static pw.Widget _sign(String label) => pw.SizedBox(
        width: 170,
        child: pw.Column(children: [
          pw.Container(height: 0.8, color: PdfKit.muted),
          pw.SizedBox(height: 3),
          pw.Text(label, style: PdfKit.t(8, color: PdfKit.muted)),
        ]),
      );

  /// One grid per calendar month in the period.
  static List<pw.Widget> _matrices(AttendanceMatrix m) {
    final from = D.parse(m.from);
    final to = D.parse(m.to);
    final out = <pw.Widget>[];
    var month = DateTime(from.year, from.month);
    while (!month.isAfter(DateTime(to.year, to.month))) {
      final first = month.isBefore(from) ? from : month;
      final last = D.lastOfMonth(month).isAfter(to) ? to : D.lastOfMonth(month);
      out.add(pw.Text(D.monthTitle(month),
          style: PdfKit.t(10, bold: true, color: PdfKit.brandDark)));
      out.add(pw.SizedBox(height: 3));
      out.add(_grid(m, D.range(first, last)));
      out.add(pw.SizedBox(height: 12));
      month = DateTime(month.year, month.month + 1);
    }
    return out;
  }

  static pw.Widget _grid(AttendanceMatrix m, List<DateTime> days) {
    final dayW = days.length > 31 ? 11.0 : 12.5;
    final widths = <int, pw.TableColumnWidth>{
      0: const pw.FixedColumnWidth(96),
      for (var i = 0; i < days.length; i++) i + 1: pw.FixedColumnWidth(dayW),
      days.length + 1: const pw.FixedColumnWidth(30),
    };
    pw.Widget small(String s, {bool bold = false, PdfColor? color, PdfColor? bg}) => pw.Container(
          height: 15,
          color: bg,
          alignment: pw.Alignment.center,
          child: pw.Text(s, style: PdfKit.t(6.8, bold: bold, color: color)),
        );
    return pw.Table(
      columnWidths: widths,
      border: pw.TableBorder.all(color: PdfKit.line, width: 0.4),
      children: [
        pw.TableRow(
          decoration: pw.BoxDecoration(color: PdfKit.brand),
          children: [
            pw.Container(
              height: 15,
              alignment: pw.Alignment.centerLeft,
              padding: const pw.EdgeInsets.only(left: 4),
              child: pw.Text('Labour', style: PdfKit.t(7, bold: true, color: PdfColors.white)),
            ),
            for (final d in days)
              small('${d.day}', bold: true, color: PdfColors.white,
                  bg: d.weekday == DateTime.sunday ? PdfKit.brandDark : null),
            small('Days', bold: true, color: PdfColors.white),
          ],
        ),
        for (final r in m.rows)
          pw.TableRow(
            children: [
              pw.Container(
                height: 15,
                alignment: pw.Alignment.centerLeft,
                padding: const pw.EdgeInsets.only(left: 4),
                child: pw.Text(r.labour.name,
                    maxLines: 1, style: PdfKit.t(7.2), overflow: pw.TextOverflow.clip),
              ),
              for (final d in days)
                () {
                  final c = r.cells[D.ymd(d)];
                  if (c == null) {
                    return small('', bg: d.weekday == DateTime.sunday ? PdfKit.zebra : null);
                  }
                  return small(c.letter,
                      bold: true, color: PdfKit.statusColor(c.letter), bg: PdfKit.statusBg(c.letter));
                }(),
              small(
                num1(days.fold(0.0, (a, d) => a + (r.cells[D.ymd(d)]?.value ?? 0))),
                bold: true,
              ),
            ],
          ),
      ],
    );
  }
}
