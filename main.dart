import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:test_app/admin/bar/ConBar.dart';
import 'package:test_app/admin/page_screen/main_screen/chat/model/chat.dart';
import 'package:test_app/users/bar/ConBar.dart'; // สมมติว่าหน้า ConBar เป็นหน้าหลักหลังล็อกอิน
import 'package:test_app/users/page_screen/main_screen/exercise/model_exercise/exercise.dart';
import 'package:test_app/users/page_screen/main_screen/exercise/providers_exerise/exercise_provider.dart';
import 'package:test_app/users/page_screen/main_screen/food/add_calories.dart/add_calories.dart';
import 'package:test_app/users/page_screen/main_screen/food/add_calories.dart/provider.dart';
import 'package:test_app/users/page_screen/main_screen/food/food_screen.dart';
import 'package:test_app/users/page_screen/main_screen/food/model_food/food.dart';
import 'package:test_app/users/page_screen/main_screen/food/providers_food/food_provider.dart';
import 'package:test_app/users/page_screen/main_screen/notify/providers_notify/notify_provider.dart';
import 'package:test_app/users/page_screen/main_screen/topic/model/topic.dart';
import 'package:test_app/users/page_screen/main_screen/topic/providers/provider_topic.dart';
import 'package:test_app/users/page_screen/main_screen/topic/show_topic_screen.dart';
import 'package:test_app/users/page_screen/nav_screen/dialy/my_dialy_food.dart';
import 'package:test_app/users/page_screen/nav_screen/profile/profile_screen.dart';
import 'package:test_app/users/page_screen/nav_screen/profile/provider/model/user.dart';
import 'package:test_app/users/page_screen/nav_screen/profile/provider/user_provider.dart';
import 'package:test_app/users/page_screen/nav_screen/profile/settings/profile_settings.dart';
import 'package:test_app/users/page_screen/nav_screen/profile/settings/update_user.dart';
import 'package:test_app/welcome/loading_screen.dart';
import 'package:test_app/welcome/welcome.dart';
import 'package:test_app/test.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await Firebase.initializeApp(); // Initialize Firebase

  runApp(
    MultiProvider(
      providers: [
        Provider(create: (context) => Provider_category_food()),
        Provider(create: (context) => Provider_exercise_history()),
        Provider(create: (context) => Provider_category_exercise()),
        Provider(create: (context) => Provider_notify()),
        Provider(create: (context) => Provider_exercise_list()),
        ChangeNotifierProvider(create: (_) => UserProfile()),
        ChangeNotifierProvider(create: (_) => TopicData()),
        ChangeNotifierProvider(create: (_) => FoodList()),
        ChangeNotifierProvider(
            create: (_) => Dialy_food()), // กำหนด Provider ตรงนี้
        ChangeNotifierProvider(create: (context) => CaloriesProvider()),
        ChangeNotifierProvider(create: (context) => Exercise_list()),
        ChangeNotifierProvider(create: (context) => DialyExercise()),
        ChangeNotifierProvider(create: (context) => ChatData())
      ],
      child: MyApp(),
    ),
  );
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      home: AuthCheck(), // ตรวจสอบสถานะการเข้าสู่ระบบ
      debugShowCheckedModeBanner: false,
    );
  }
}

class AuthCheck extends StatefulWidget {
  @override
  _AuthCheckState createState() => _AuthCheckState();
}

class _AuthCheckState extends State<AuthCheck> {
  final dialyCaloriesService _dialyCaloriesService = dialyCaloriesService();
  final UserDataFetcher dataFetcher = UserDataFetcher();
  late Future<void> _updateCaloriesNow;
  bool isLoading = true; // ตัวแปรเพื่อจัดการการแสดงผล UI

  @override
  void initState() {
    super.initState();
    _loadInitialData();
    dataFetcher
        .fetchAndSaveUserData(context); // เรียกฟังก์ชันโหลดข้อมูลตอนเริ่มต้น
  }

  // ฟังก์ชันโหลดข้อมูล
  Future<void> _loadInitialData() async {
    try {
      await Future.delayed(
          Duration(milliseconds: 500)); // เลื่อนการโหลดข้อมูลออกไปเล็กน้อย
      await dataFetcher.fetchAndSaveUserData(context); // ดึงข้อมูลผู้ใช้
      _updateCaloriesNow = updateCaloriesNow(context); // อัปเดตแคลอรี่
    } catch (e) {
      print("เกิดข้อผิดพลาด: $e");
    } finally {
      setState(() {
        isLoading = false; // ปิดสถานะการโหลดเมื่อเสร็จสิ้น
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final userProvider = Provider.of<UserProfile>(context, listen: false);

    if (isLoading) {
      return Center(
          child: LoadingScreen()); // แสดงหน้าโหลดข้อมูลเมื่อข้อมูลยังไม่เสร็จ
    }

    return StreamBuilder<User?>(
      stream: FirebaseAuth.instance.authStateChanges(),
      builder: (context, authSnapshot) {
        if (authSnapshot.connectionState == ConnectionState.waiting) {
          return Center(
              child: LoadingScreen()); // แสดงหน้าโหลดเมื่อกำลังตรวจสอบสถานะ
        } else if (authSnapshot.hasData) {
          // ตรวจสอบบทบาทหลังจากข้อมูลถูกดึงเรียบร้อย
          if (userProvider.role == 0) {
            return ConBar();
          } else if (userProvider.role == 1) {
            return ConBarAdmin();
          } else {
            return Center(child: LoadingScreen());
          }
        } else {
          // กรณียังไม่ได้เข้าสู่ระบบ
          return WelcomeScreen();
        }
      },
    );
  }
}
