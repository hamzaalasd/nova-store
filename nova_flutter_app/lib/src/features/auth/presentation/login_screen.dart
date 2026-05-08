import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../../core/network/api_client.dart';
import '../../../core/storage/token_store.dart';
import '../../../core/theme/nova_colors.dart';
import '../../../core/widgets/back_to_home_guard.dart';
import '../../../core/widgets/nova_brand_mark.dart';
import '../data/auth_repository.dart';

class LoginScreen extends ConsumerStatefulWidget {
  const LoginScreen({super.key});

  @override
  ConsumerState<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends ConsumerState<LoginScreen> {
  final _email = TextEditingController(text: 'customer@nova.test');
  final _password = TextEditingController(text: 'password');
  final _name = TextEditingController(text: 'عميل NOVA');
  final _phone = TextEditingController(text: '0500000002');
  bool _registerMode = false;
  bool _hidePassword = true;

  @override
  void dispose() {
    _email.dispose();
    _password.dispose();
    _name.dispose();
    _phone.dispose();
    super.dispose();
  }

  Future<void> _continueAsGuest() async {
    await ref.read(tokenStoreProvider).completeOnboarding();
    if (!mounted) return;
    context.go('/');
  }

  @override
  Widget build(BuildContext context) {
    ref.listen(authStateProvider, (previous, next) {
      if (next.hasValue && next.value != null) context.go('/');
      if (next.hasError) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(readableApiError(next.error!))),
        );
      }
    });

    final auth = ref.watch(authStateProvider);

    return BackToHomeGuard(
      child: Scaffold(
        backgroundColor: NovaColors.deepNight,
        body: SafeArea(
        child: ListView(
          padding: const EdgeInsets.fromLTRB(18, 22, 18, 24),
          children: [
            Row(
              children: [
                IconButton(
                  onPressed: () => context.go('/'),
                  color: NovaColors.darkMuted,
                  icon: const Icon(Icons.close),
                ),
                const Spacer(),
                TextButton(
                  onPressed: _continueAsGuest,
                  child: const Text('دخول كزائر', style: TextStyle(color: NovaColors.gold)),
                ),
              ],
            ),
            const SizedBox(height: 12),
            const _LoginLogo(),
            const SizedBox(height: 28),
            Text(
              _registerMode ? 'حساب جديد يليق بتجربتك' : 'مرحبا بعودتك',
              style: const TextStyle(color: NovaColors.cream, fontSize: 28, fontWeight: FontWeight.w900),
            ),
            const SizedBox(height: 8),
            Text(
              _registerMode ? 'سجل بياناتك لحفظ السلة وتتبع الطلبات.' : 'سجل دخولك لإكمال رحلة التسوق في NOVA.',
              style: const TextStyle(color: NovaColors.muted, height: 1.7, fontWeight: FontWeight.w700),
            ),
            const SizedBox(height: 24),
            if (_registerMode) ...[
              _DarkInput(controller: _name, label: 'الاسم الكامل', icon: Icons.person_outline),
              const SizedBox(height: 12),
              _DarkInput(controller: _phone, label: 'رقم الجوال', icon: Icons.phone_outlined, keyboardType: TextInputType.phone),
              const SizedBox(height: 12),
            ],
            _DarkInput(controller: _email, label: 'البريد الإلكتروني', icon: Icons.mail_outline, keyboardType: TextInputType.emailAddress),
            const SizedBox(height: 12),
            _DarkInput(
              controller: _password,
              label: 'كلمة المرور',
              icon: Icons.lock_outline,
              obscureText: _hidePassword,
              suffix: IconButton(
                onPressed: () => setState(() => _hidePassword = !_hidePassword),
                icon: Icon(_hidePassword ? Icons.visibility_outlined : Icons.visibility_off_outlined, color: NovaColors.muted),
              ),
            ),
            Align(
              alignment: Alignment.centerLeft,
              child: TextButton(onPressed: () {}, child: const Text('نسيت كلمة المرور؟', style: TextStyle(color: NovaColors.gold))),
            ),
            const SizedBox(height: 4),
            SizedBox(
              height: 54,
              child: FilledButton(
                onPressed: auth.isLoading
                    ? null
                    : () {
                        if (_registerMode) {
                          ref.read(authStateProvider.notifier).register(_name.text, _email.text, _password.text, _phone.text);
                        } else {
                          ref.read(authStateProvider.notifier).login(_email.text, _password.text);
                        }
                      },
                style: FilledButton.styleFrom(
                  backgroundColor: NovaColors.violet,
                  foregroundColor: NovaColors.cream,
                  shape: const StadiumBorder(),
                ),
                child: Text(
                  auth.isLoading ? 'جاري التنفيذ...' : (_registerMode ? 'إنشاء الحساب' : 'تسجيل الدخول'),
                  style: const TextStyle(fontWeight: FontWeight.w900),
                ),
              ),
            ),
            const SizedBox(height: 16),
            const Row(
              children: [
                Expanded(child: Divider(color: NovaColors.darkBorder)),
                Padding(
                  padding: EdgeInsets.symmetric(horizontal: 12),
                  child: Text('أو', style: TextStyle(color: NovaColors.muted)),
                ),
                Expanded(child: Divider(color: NovaColors.darkBorder)),
              ],
            ),
            const SizedBox(height: 16),
            const Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                _SocialButton(icon: Icons.g_mobiledata),
                SizedBox(width: 10),
                _SocialButton(icon: Icons.apple),
                SizedBox(width: 10),
                _SocialButton(icon: Icons.phone_iphone),
              ],
            ),
            const SizedBox(height: 18),
            TextButton(
              onPressed: () => setState(() => _registerMode = !_registerMode),
              child: Text(
                _registerMode ? 'لديك حساب؟ سجل الدخول' : 'ليس لديك حساب؟ سجل الآن',
                style: const TextStyle(color: NovaColors.gold, fontWeight: FontWeight.w900),
              ),
            ),
          ],
        ),
        ),
      ),
    );
  }
}

class _LoginLogo extends StatelessWidget {
  const _LoginLogo();

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        const NovaBrandMark(size: 38),
        const SizedBox(width: 14),
        const Text.rich(
          TextSpan(text: 'Nova ', children: [TextSpan(text: 'Store', style: TextStyle(color: NovaColors.gold))]),
          style: TextStyle(color: NovaColors.cream, fontSize: 24, fontWeight: FontWeight.w900),
        ),
      ],
    );
  }
}

class _DarkInput extends StatelessWidget {
  const _DarkInput({
    required this.controller,
    required this.label,
    required this.icon,
    this.keyboardType,
    this.obscureText = false,
    this.suffix,
  });

  final TextEditingController controller;
  final String label;
  final IconData icon;
  final TextInputType? keyboardType;
  final bool obscureText;
  final Widget? suffix;

  @override
  Widget build(BuildContext context) {
    return TextField(
      controller: controller,
      keyboardType: keyboardType,
      obscureText: obscureText,
      style: const TextStyle(color: NovaColors.cream, fontWeight: FontWeight.w700),
      decoration: InputDecoration(
        labelText: label,
        prefixIcon: Icon(icon, color: NovaColors.violet),
        suffixIcon: suffix,
        filled: true,
        fillColor: NovaColors.darkPurple,
        labelStyle: const TextStyle(color: NovaColors.darkMuted),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: const BorderSide(color: NovaColors.darkBorder),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: const BorderSide(color: NovaColors.gold, width: 1.3),
        ),
      ),
    );
  }
}

class _SocialButton extends StatelessWidget {
  const _SocialButton({required this.icon});

  final IconData icon;

  @override
  Widget build(BuildContext context) {
    return Container(
      width: 52,
      height: 42,
      decoration: BoxDecoration(
        color: NovaColors.darkPurple,
        borderRadius: BorderRadius.circular(13),
        border: Border.all(color: NovaColors.darkBorder),
      ),
      child: Icon(icon, color: NovaColors.darkMuted),
    );
  }
}
