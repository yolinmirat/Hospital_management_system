<header>
    <div class="container header-content">
        <div class="logo">
            <i class="fas fa-hospital"></i>
            <h1>MediCare: For Healthy Life</h1>
        </div>
        <nav>
            <ul>
                <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="about.php"><i class="fas fa-info-circle"></i> About</a></li>
                <li><a href="contact.php"><i class="fas fa-phone"></i> Contact</a></li>
            </ul>
        </nav>
        <div class="user-info">
            <?php if (isset($_SESSION['user_name'])): ?>
                <span>Welcome, <?php echo $_SESSION['user_name']; ?></span>
                <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php else: ?>
                <span>Please log in</span>
                <a href="index.php" class="btn"><i class="fas fa-sign-in-alt"></i> Login</a>
            <?php endif; ?>
        </div>
    </div>
</header>