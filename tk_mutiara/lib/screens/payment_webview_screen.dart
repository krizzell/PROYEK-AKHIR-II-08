import 'package:flutter/material.dart';
import 'package:webview_flutter/webview_flutter.dart';

class PaymentWebViewScreen extends StatefulWidget {
  final String initialUrl;

  const PaymentWebViewScreen({super.key, required this.initialUrl});

  @override
  State<PaymentWebViewScreen> createState() => _PaymentWebViewScreenState();
}

class _PaymentWebViewScreenState extends State<PaymentWebViewScreen> {
  late final WebViewController _controller;
  int _loadingProgress = 0;
  bool _hasClosed = false;

  @override
  void initState() {
    super.initState();
    _controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setNavigationDelegate(
        NavigationDelegate(
          onProgress: (progress) {
            if (!mounted) return;
            setState(() => _loadingProgress = progress);
          },
          onNavigationRequest: (request) {
            if (_shouldClosePaymentPage(request.url)) {
              _closePaymentPage(request.url);
              return NavigationDecision.prevent;
            }

            return NavigationDecision.navigate;
          },
          onPageStarted: (url) {
            if (_shouldClosePaymentPage(url)) {
              _closePaymentPage(url);
            }
          },
        ),
      )
      ..loadRequest(Uri.parse(widget.initialUrl));
  }

  bool _shouldClosePaymentPage(String url) {
    final uri = Uri.tryParse(url);
    final lowerUrl = url.toLowerCase();
    final host = uri?.host.toLowerCase() ?? '';

    return host == 'example.com' ||
        host.endsWith('.example.com') ||
        lowerUrl.contains('finish') ||
        lowerUrl.contains('success') ||
        lowerUrl.contains('status_code=200') ||
        lowerUrl.contains('transaction_status=settlement') ||
        lowerUrl.contains('transaction_status=capture');
  }

  void _closePaymentPage(String url) {
    if (_hasClosed || !mounted) return;
    _hasClosed = true;
    Navigator.pop(context, url);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Pembayaran Midtrans')),
      body: Stack(
        children: [
          WebViewWidget(controller: _controller),
          if (_loadingProgress < 100)
            LinearProgressIndicator(value: _loadingProgress / 100),
        ],
      ),
    );
  }
}
