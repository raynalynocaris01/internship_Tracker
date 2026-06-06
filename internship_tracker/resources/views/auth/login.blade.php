<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Internship Tracker</title>
    @vite(['resources/js/app.js'])
</head>
<body>

    <div class="login-container">
        <div class="login-card">
            <div class="card-header">
                <h2>Internship Tracker</h2>
                <p>Student & Teacher Portal</p>
            </div>
            <div class="card-body">
                
                @if ($errors->any())
                    <div class="alert-danger">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                
                @if (session('success'))
                    <div class="alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if (request()->get('redirect') === 'qr')
                    <div class="qr-info">📱 Please login to complete your time request.</div>
                @endif
                
                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus autocomplete="username">
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input id="password" type="password" name="password" class="form-control" required autocomplete="current-password">
                    </div>

                    <button type="submit" class="btn-login">Login</button>
                </form>
                
                <!-- Register Link -->
                <div class="register-link">
                    <p>Don't have an account? <a href="#" onclick="showRegistration()">Register here</a></p>
                </div>
                
                <div class="footer-note">Accounts are managed by your administrator</div>
            </div>
        </div>
    </div>

    <!-- Registration Modal -->
    <div id="registerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Register New Account</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="form-group">
                        <label for="reg_name">Full Name</label>
                        <input id="reg_name" type="text" name="name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="reg_email">Email</label>
                        <input id="reg_email" type="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="reg_role">Register As</label>
                        <select id="reg_role" name="role" class="form-control" required onchange="toggleRoleFields()">
                            <option value="">Select Role</option>
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>
                        </select>
                    </div>

                    <!-- Student Fields -->
                    <div id="studentFields" class="role-fields" style="display: none;">
                        <div class="form-group">
                            <label for="student_id">Student ID</label>
                            <input id="student_id" type="text" name="student_id" class="form-control" placeholder="e.g., 2024001">
                        </div>

                        <div class="form-group">
                            <label for="course">Course</label>
                            <select id="course" name="course" class="form-control">
                                <option value="">Select Course</option>
                                <option value="BSIT">BS Information Technology</option>
                                <option value="BSCS">BS Computer Science</option>
                                <option value="BSIS">BS Information Systems</option>
                                <option value="BSECE">BS Electronics Engineering</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="year_level">Year Level</label>
                            <select id="year_level" name="year_level" class="form-control">
                                <option value="">Select Year Level</option>
                                <option value="1">1st Year</option>
                                <option value="2">2nd Year</option>
                                <option value="3">3rd Year</option>
                                <option value="4">4th Year</option>
                            </select>
                        </div>
                    </div>

                    <!-- Teacher Fields -->
                    <div id="teacherFields" class="role-fields" style="display: none;">
                        <div class="form-group">
                            <label for="teacher_id">Teacher ID</label>
                            <input id="teacher_id" type="text" name="teacher_id" class="form-control" placeholder="e.g., 1001">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="department">Department</label>
                        <select id="department" name="department" class="form-control">
                            <option value="">Select Department</option>
                            <option value="Computer Science">Computer Science</option>
                            <option value="Information Technology">Information Technology</option>
                            <option value="Engineering">Engineering</option>
                            <option value="Business">Business</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="reg_password">Password</label>
                        <input id="reg_password" type="password" name="password" class="form-control" required>
                        <small class="form-text">Password must be at least 8 characters</small>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required>
                    </div>

                    <button type="submit" class="btn-login">Register</button>
                </form>
            </div>
        </div>
    </div>

    <style>
        body {
            background-color: #f3f4f6;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        .login-container {
            width: 100%;
            max-width: 450px;
            margin: 100px auto;
            padding: 20px;
            box-sizing: border-box;
        }
        .login-card {
            background: white;
            border-radius: 28px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            overflow: hidden;
        }
        .card-header {
            background: #216699;
            color: #ede432;
            padding: 30px 20px;
            text-align: center;
            border-bottom: 3px solid #ede432;
        }
        .card-header h2 {
            font-size: 1.8rem;
            margin: 0 0 8px 0;
            font-weight: bold;
        }
        .card-header p {
            margin: 0;
            opacity: 0.9;
        }
        .card-body {
            padding: 35px 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #216699;
        }
        .form-control {
            width: 100%;
            padding: 12px 18px;
            border: 1px solid #ccc;
            border-radius: 40px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .form-control:focus {
            outline: none;
            border-color: #216699;
            box-shadow: 0 0 5px rgba(33, 102, 153, 0.3);
        }
        select.form-control {
            background-color: white;
            cursor: pointer;
        }
        .btn-login {
            width: 100%;
            background: #216699;
            color: white;
            border: 2px solid #ede432;
            border-radius: 40px;
            padding: 12px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s ease;
        }
        .btn-login:hover {
            background: #174b73;
        }
        .alert-danger {
            background-color: #fde8e8;
            color: #e53e3e;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        .alert-success {
            background-color: #def5e5;
            color: #1e7e34;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        .qr-info {
            background-color: #ebf8ff;
            color: #2b6cb0;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            text-align: center;
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
        }
        .register-link a {
            color: #216699;
            text-decoration: none;
            font-weight: bold;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
        .footer-note {
            text-align: center;
            color: #777;
            font-size: 0.85rem;
            margin-top: 25px;
        }
        .form-text {
            font-size: 0.75rem;
            color: #666;
            margin-top: 5px;
            display: block;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            overflow-y: auto;
        }
        .modal-content {
            background-color: white;
            margin: 50px auto;
            max-width: 500px;
            width: 90%;
            border-radius: 28px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            animation: modalSlideIn 0.3s ease-out;
        }
        @keyframes modalSlideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        .modal-header {
            background: #216699;
            color: #ede432;
            padding: 20px;
            border-radius: 28px 28px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
        }
        .close {
            color: #ede432;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.2s;
        }
        .close:hover {
            color: white;
        }
        .modal-body {
            padding: 30px;
            max-height: 500px;
            overflow-y: auto;
        }
        
        /* Role fields styling */
        .role-fields {
            border-left: 3px solid #216699;
            padding-left: 15px;
            margin-bottom: 20px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .login-container {
                margin: 50px auto;
                padding: 15px;
            }
            .card-body, .modal-body {
                padding: 25px 20px;
            }
        }
    </style>

    <script>
        function showRegistration() {
            document.getElementById('registerModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
        
        function closeModal() {
            document.getElementById('registerModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        function toggleRoleFields() {
            const role = document.getElementById('reg_role').value;
            const studentFields = document.getElementById('studentFields');
            const teacherFields = document.getElementById('teacherFields');
            
            // Hide both first
            studentFields.style.display = 'none';
            teacherFields.style.display = 'none';
            
            // Make fields not required
            document.getElementById('student_id').required = false;
            document.getElementById('course').required = false;
            document.getElementById('year_level').required = false;
            document.getElementById('teacher_id').required = false;
            
            // Show based on selected role and set required
            if (role === 'student') {
                studentFields.style.display = 'block';
                document.getElementById('student_id').required = true;
                document.getElementById('course').required = true;
                document.getElementById('year_level').required = true;
            } else if (role === 'teacher') {
                teacherFields.style.display = 'block';
                document.getElementById('teacher_id').required = true;
            }
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('registerModal');
            if (event.target === modal) {
                closeModal();
            }
        }
        
        // Close modal on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>