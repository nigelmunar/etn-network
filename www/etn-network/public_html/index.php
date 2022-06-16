<?php
    declare(strict_types = 1);
    
    require_once __DIR__ . '/../application/config/config.php';
	require_once ROOT_PATH . 'application/includes/init.php';
    require_once ROOT_PATH . 'application/tools/dateFunctions.php';
    require_once ROOT_PATH . 'application/tools/filterFunctions.php';
	require_once ROOT_PATH . 'application/tools/generalFunctions.php';
    require_once ROOT_PATH . 'application/factories/MovieDatabaseFactory.php';

    $errors = [];

    $movieDB = \MovieDatabaseFactory::create();

    $title          = '';
    $genre          = '';
    $description    = '';
    $releaseDate    ='';

    if(isset($_POST['txtFormType']) && $_POST['txtFormType'] === "ADDMOVIE")
    {
        $title    = (isset($_POST['txtTitle']) ? (trim(mb_substr(trim($_POST['txtTitle']), 0, 200))) : '');
        $genre    = (isset($_POST['txtGenre']) ? (trim(mb_substr(trim($_POST['txtGenre']), 0, 200))) : '');
        $description  = (isset($_POST['txtDescription']) ? (trim(mb_substr(trim($_POST['txtDescription']), 0, 200))) : '');
        $releaseDate  = (isset($_POST['txtReleaseDate']) ? (trim(mb_substr(trim($_POST['txtReleaseDate']), 0, 4))) : '');
        

        if(strlen($title) == 0)
        {
            $errors[] = 'Title';
            $errors[] = 'TitleBlank';
        }

        if(strlen($genre) == 0)
        {
            $errors[] = 'Genre';
            $errors[] = 'GenreBlank';
        }

        if(strlen($releaseDate) == 0)
        {
            $errors[] = 'ReleaseDate';
            $errors[] = 'ReleaseDateBlank';
        } 
        elseif(!preg_match("/^\d{4}$/",$releaseDate)) 
        {
            $errors[] = 'ReleaseDate';
            $errors[] = 'ReleaseDateFormat';
        } 


        if(count($errors) == 0)
        {
            $movie = new \Entities\Movie();

            $movie->setTitle($title);
            $movie->setGenre($genre);
            $movie->setDescription($description);
            $movie->setReleaseDate($releaseDate);

            $movieDB->addMovie($movie);


            $_SESSION['Movie Added'] = 'true';

            header('Location: ' . $siteURL );
            exit;
        }
    
    }


    if(isset($_GET['delete']) && strlen($_GET['delete']) > 0)
    {
        $movieCode = $_GET['delete'];

        $movieDB->deleteMovie($movieCode);

        $_SESSION['Movie Deleted'] = 'true';

        header('Location: ' . $siteURL );
        exit;

    }

    require ROOT_PATH . 'application/config/pageVars.php';
	require ROOT_PATH . 'application/html-includes/admin/header.php';
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Dashboard</h1>
                </div>
                <div class="col-sm-6">
                   
                    <?php
                        require ROOT_PATH . 'application/html-includes/admin/breadcrumb.php';
                    ?>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>   

  <!-- Main content -->
  <section class="content">
        <div class="container-fluid">
			<div class="row">
				<div class="col-sm-2">
					<div class="card card-primary">
						<div class="card-body">
							<?php
                                require ROOT_PATH . 'application/forms/movie.php';
							?>
						</div>
                    </div>
				</div>
                <div class="col-sm-7">
                    <div class="card card-primary">
                        <div class="card-content collapse show">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="movie-datatable" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Title</th>
                                                <th>Description</th>
                                                <th>Genre</th>
                                                <th>Release Date</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
		</div>
	</section>
</div>

<script> 


</script>

<?php
    require ROOT_PATH . 'application/html-includes/admin/footer.php';

