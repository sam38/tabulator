<div class="container">
    <div class="row">
        <div class="col-md-6 offset-md-3 col-xl-4 offset-xl-4 py-4">
            <form action="" method="post" class="py-4">
                <?php if ($loginId == -1) { ?>
                    <div class="alert alert-danger text-center" role="alert">
                        Incorrect username or password!
                    </div>
                <?php } else { ?>
                    <div class="alert alert-primary text-center" role="alert">
                        Enter the passcode to view this private event!
                    </div>
                <?php } ?>
                <div class="mb-3 row">
                    <label for="username" class="col-sm-3 col-form-label">Username</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="username"
                               name="username" value="" autofocus required>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="inputPassword" class="col-sm-3 col-form-label">Password</label>
                    <div class="col-sm-9">
                        <input type="password" class="form-control" id="inputPassword"
                               name="password" value="" required>
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary">Login</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
