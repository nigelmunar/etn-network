<?php
    declare(strict_types=1);
?>
<form class="form-horizontal" action="<?php echo $pageURL; ?>" method="post">
    <?php
        if(count($errors) > 0)
        {
            echo '<ul class="alert alert-danger list-unstyled">';

            if(in_array('TitleBlank', $errors))
            {
                echo '<li class="member-account__error">Title cannot be empty.</li>';
            }

            if(in_array('GenreBlank', $errors))
            {
                echo '<li class="member-account__error">Genre cannot be blank.</li>';
            }

            if(in_array('ReleaseDateBlank', $errors))
            {
                echo '<li class="member-account__error">Releas Date cannot be empty.</li>';
            }

            if(in_array('ReleaseDateFormat', $errors))
            {
                echo '<li class="member-account__error">Release date format is invalid.</li>';
            }
            echo '</ul>';
        }
    ?>
    <fieldset class="form-group">Title
        <input type="text" class="form-control<?php echo (in_array('Title', $errors) ? ' is-invalid' : ''); ?>" name="txtTitle" value="<?php echo htmlspecialchars($title); ?>" placeholder="The Godfather" />
    </fieldset>

    <fieldset class="form-group">Genre
        <input type="text" class="form-control<?php echo (in_array('Genre', $errors) ? ' is-invalid' : ''); ?>" name="txtGenre" value="<?php echo htmlspecialchars($genre); ?>" placeholder="Crime"/>
    </fieldset>

    <fieldset class="form-group">Description
        <textarea class="form-control<?php echo (in_array('Description', $errors) ? ' is-invalid' : ''); ?>" rows="3" name="txtDescription" placeholder="Don Vito Corleone, head of a mafia family, decides to hand over his empire to his youngest son Michael. "></textarea>
    </fieldset>


    <fieldset class="form-group">Release Date
        <input type="text" class="form-control<?php echo (in_array('ReleaseDate', $errors) ? ' is-invalid' : ''); ?>" name="txtReleaseDate" value="<?php echo ($releaseDate); ?>" placeholder="1972"/>
    </fieldset>

    <input type="hidden" name="txtFormType" value="<?php echo (isset($activeMovie) ? 'SAVEMOVIE' : 'ADDMOVIE'); ?>" />
    <input type="submit" class="btn btn-primary" style="background-color: #130B91;" name="" value="Add" />&nbsp;&nbsp;&nbsp;or&nbsp;&nbsp;&nbsp;<a href="<?php echo $siteURL?>" style="color: #130B91;">cancel</a>
</form>