    <?php
      $inputPrefix = $data['punishmentFormPrefix'];
      $htmlPrefix = "p" . $data['punishmentFormId'];
      $id = $data['punishmentFormId'];
      $name = $data['punishmentFormName'];

      $punishment = $data["punishmentFormPunishment"];
      $datePicker = $data["punishmentFormDatePicker"];
      $datePickerError = $data["punishmentFormDatePickerError"];
      $justification = $data["punishmentFormJustification"];
      $justificationError = $data["punishmentFormJustificationError"];
      
      setQuillAddField($htmlPrefix . "justification", $inputPrefix . "[justification]");
    ?>
    <div class="form-group">
      <label for="<?php echo $inputPrefix; ?>[punishment]">Punishment for <strong><?php echo $name; ?></strong></label>
      <select class="form-control" name="<?php echo $inputPrefix; ?>[punishment]" data-prefix="<?php echo $htmlPrefix; ?>">
        <option value="">No punishment</option>
        <option value="warning" <?php if(isset($punishment) && $punishment == "warning") echo "selected"; ?>>Warning</option>
        <option value="temporaryBan" <?php if(isset($punishment) && $punishment == "temporaryBan") echo "selected"; ?>>Temporary ban</option>
        <option value="permanentBan" <?php if(isset($punishment) && $punishment == "permanentBan") echo "selected"; ?>>Permanent ban</option>
      </select>
    </div>

    <div id="<?php echo $htmlPrefix; ?>inputGroup" <?php if(empty($punishment)) echo "class='d-none'"; ?>>
      <div class="form-group <?php if(!empty($punishment) && $punishment != "temporaryBan") echo "d-none"; ?>"
            id="<?php echo $htmlPrefix; ?>datePickerGroup">
        <label for="<?php echo $htmlPrefix; ?>datePicker">Banned until: <sup>*</sup></label>
        <div class="input-group date datepicker-group" id="<?php echo $htmlPrefix; ?>datePicker" data-target-input="nearest">
            <input type="text"
                    name="<?php echo $inputPrefix; ?>[datePicker]"
                    class="form-control datepicker-input <?php echo getValidationClass($datePickerError); ?>"
                    data-target="#<?php echo $htmlPrefix; ?>datePicker"
                    value="<?php echo $datePicker; ?>"/>

            <div class="input-group-append" data-target="#<?php echo $htmlPrefix; ?>datePicker" data-toggle="datetimepicker">
                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
            </div>
            <span class="invalid-feedback"><?php echo $datePickerError; ?></span>
        </div>
      </div>
      <div class="form-group">
        <label for="textarea">Justification: <sup>*</sup></label>
        <div id="<?php echo $htmlPrefix; ?>justification" class="textarea form-control <?php echo getValidationClass($justificationError) ?>">
        <?php echo MarkdownToHTML($justification); ?></div>
        <span class="invalid-feedback">
          <?php echo $justificationError; ?>
        </span>
      </div>
    </div>
    <br/>
