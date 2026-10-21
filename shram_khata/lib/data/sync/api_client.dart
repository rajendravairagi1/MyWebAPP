import 'dart:async';
import 'dart:convert';
import 'dart:io';

/// The HazriBook cloud (your Bluehost subdomain).
const defaultServerUrl = 'https://hazribook.oneweblink.com';

/// The server answered with an error (wrong password, no permission ...).
class ApiException implements Exception {
  ApiException(this.status, this.code, this.message);
  final int status;
  final String code;
  final String message;
  @override
  String toString() => message;
}

/// The server could not be reached.
class OfflineException implements Exception {
  @override
  String toString() => 'No internet connection. Your data is safe on this phone and will upload later.';
}

/// Minimal JSON-over-HTTPS client.
class ApiClient {
  ApiClient({this.baseUrl = defaultServerUrl, this.token, this.deviceName = ''});

  final String baseUrl;
  String? token;
  final String deviceName;

  Future<Map<String, dynamic>> get(String path, {Map<String, String>? query}) =>
      _send('GET', path, query: query);

  Future<Map<String, dynamic>> post(String path, [Map<String, Object?>? body]) =>
      _send('POST', path, body: body ?? const {});

  Future<Map<String, dynamic>> _send(String method, String path,
      {Map<String, Object?>? body, Map<String, String>? query}) async {
    final uri = Uri.parse('$baseUrl$path').replace(queryParameters: query);
    final client = HttpClient()..connectionTimeout = const Duration(seconds: 12);
    try {
      final req = await client.openUrl(method, uri).timeout(const Duration(seconds: 15));
      req.headers.set('Accept', 'application/json');
      if (token != null) req.headers.set('Authorization', 'Bearer $token');
      if (body != null) {
        req.headers.contentType = ContentType.json;
        req.add(utf8.encode(jsonEncode(body)));
      }
      final res = await req.close().timeout(const Duration(seconds: 45));
      final text = await res.transform(utf8.decoder).join().timeout(const Duration(seconds: 45));
      Map<String, dynamic> json;
      try {
        json = (jsonDecode(text) as Map).cast<String, dynamic>();
      } catch (_) {
        throw ApiException(res.statusCode, 'bad_response', 'The server sent an unexpected answer. Try again later.');
      }
      if (json['ok'] != true) {
        final e = (json['error'] as Map?)?.cast<String, dynamic>() ?? const {};
        throw ApiException(res.statusCode, '${e['code'] ?? 'error'}', '${e['message'] ?? 'Something went wrong.'}');
      }
      return json;
    } on SocketException {
      throw OfflineException();
    } on TimeoutException {
      throw OfflineException();
    } on HandshakeException {
      throw OfflineException();
    } on HttpException {
      throw OfflineException();
    } finally {
      client.close(force: true);
    }
  }
}
