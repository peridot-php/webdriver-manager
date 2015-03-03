<?php
it('should download the latest selenium standalone and chrome driver', function () {
    $this->manager->update();
    $path = $this->manager->getInstallPath();
    $selenium = glob("$path/selenium-server-standalone-*");
    $chrome = glob("$path/chromedriver*");
    expect($selenium)->to->have->length(1, 'no selenium file found');
    expect($chrome)->to->have->length->at->least(2, 'no chrome files found');
});
