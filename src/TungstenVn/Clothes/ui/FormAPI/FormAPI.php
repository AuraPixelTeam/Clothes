<?php

declare(strict_types = 1);

namespace TungstenVn\Clothes\ui\FormAPI;

use TungstenVn\Clothes\libs\jojoe77777\FormAPI\SimpleForm;
use TungstenVn\Clothes\libs\jojoe77777\FormAPI\CustomForm;
use TungstenVn\Clothes\libs\jojoe77777\FormAPI\ModelForm;
class FormAPI{

    public function createCustomForm(?callable $function = null) : CustomForm {
        return new CustomForm($function);
    }
    public function createSimpleForm(?callable $function = null) : SimpleForm {
        return new SimpleForm($function);
    }
    public function createModalForm(?callable $function = null) : ModalForm {
        return new ModalForm($function);
    }
}
