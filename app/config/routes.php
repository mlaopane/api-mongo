<?php

use CVtheque\Controller\Collection\{
    SkillController,
    ExperienceController,
    TrainingController
};

/** Database : 'cvtheque' */
$app->group('/cvtheque', function () {

    /** Database */
    // $this->get("/_show", DatabaseController::class.":show")->setName('database_show');

    /** CV */
    // $this->get("/cv", CvController::class.":get")->setName('cv_get');

    /** Skill */
    $this->get("/skill", SkillController::class.":get")->setName('skill_get');
    $this->post("/skill", SkillController::class.":post")->setName('skill_post');
    $this->put("/skill/{id:[0-9]+}", SkillController::class.":put")->setName('skill_put');
    $this->delete("/skill[/{id:[0-9]+}]", SkillController::class.":delete")->setName('skill_delete');

    /** Experience */
    $this->get("/experience", ExperienceController::class.":get")->setName('experience_get');

    /** Training */
    $this->get("/training", TrainingController::class.":get")->setName('training_get');

});
