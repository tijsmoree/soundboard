<?php

namespace app\modules\membermodels\models;
use yii\web\HttpException;

/**
 * This is the model class for table "members.pending_changes".
 *
 * @property integer $id
 * @property int $reference_id
 * @property string $reference_type
 * @property int $change_model_id
 * @property string $change_model_type
 * @property string $change_type
 * @property string $changes
 * @property string $file_path
 * @property int $resolved_by
 * @property string $resolved_resolution
 * @property string $resolved_at
 * @property string $created_at
 * @property string $updated_at
 *
 */
class PendingChange extends MemberDbRecord {

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'pending_changes';
	}

	public function getReference() {
		$class = '\app\modules\membermodels\models\\' . $this->reference_type;
		return $class::find()->where(['id' => $this->reference_id])->one();
	}

	public function getObject() {

		$class = '\app\modules\membermodels\models\\' . $this->change_model_type;
		if ($this->change_type === "update") {
			return $class::find()->where(['id' => $this->change_model_id])->one();
		} else if ($this->change_type === "create") {
			return new $class();
		}
	}

	public function getDiff() {
		$rt = [];
		if ($this->change_type === "update") {
			$object = $this->object;
			$changes = json_decode($this->changes);
			foreach($changes as $key => $value) {
				$rt[] = [
					'key' => $key,
					'old_value' => $object->$key,
					'new_value' => $value
				];
			}
		} else if ($this->change_type === "create") {
			$changes = json_decode($this->changes);
			foreach($changes as $key => $value) {
				$rt[] = [
					'key' => $key,
					'old_value' => '',
					'new_value' => $value
				];
			}
		}
		return $rt;
	}

	public function resolve($resolution) {
		$this->resolved_at = date('Y-m-d H:i:s');
		$this->resolved_resolution = $resolution;
		$this->save();
	}

	private function applyReferenceOnCreate($model, $objectType, $objectId) {
		switch($objectType) {
			case "Person":
				$model->person_id = $objectId;
				break;
			default:
				throw new HttpException(422, json_encode(["Cannot apply reference for " . $objectType]));
				break;
		}
	}

	public function accept() {
		$object = null;
		if ($this->reference_type === "PendingChange" && $this->reference->resolved_resolution !== "accepted") {
			throw new HttpException(422, json_encode(["Previous change not accepted (yet)"]));
		}
		if ($this->change_type === "update" || $this->change_type === "create") {
			/* @var MemberDbRecord $object */
			$object = $this->object;
			foreach(json_decode($this->changes) as $key => $value) {
				$object->setAttribute($key, $value);
			}
			if ($this->change_type === "create") {
				if ($this->reference_type === "PendingChange") {
					$this->applyReferenceOnCreate($object, $this->reference->change_model_type, $this->reference->change_model_id);
				} else if ($this->reference_type !== null) {
					$this->applyReferenceOnCreate($object, $this->reference_type, $this->reference_id);
				}
				if ($this->file_path !== null && $this->change_model_type === "PersonPicture") {
					$fileName = basename($this->file_path);
					$object->file_name = $object->newFileName($fileName);
				}
			}
			if(!$object->save()) {
				throw new HttpException(422, json_encode($object->errors));
			}
			if ($this->change_type === "create") {
				$this->change_model_id = $object->id;
				if ($this->file_path !== null && $this->change_model_type === "PersonPicture") {
					rename($this->file_path, $object->imageFile);
					$image = new \Eventviva\ImageResize($object->imageFile);
					$image->resizeToBestFit(PersonPicture::$maxPictureSize, PersonPicture::$maxPictureSize);
					$image->save($object->imageFile);
					$object->makeMain();
				}
			}
		} else {
			throw new HttpException(422, json_encode(["invalid change type"]));
		}

		$this->resolve('accepted');
	}

	public function decline() {
		if ($this->change_type === "create" && $this->change_model_type === "PersonPicture") {
			if (file_exists($this->file_path)) {
				unlink($this->file_path);
			}
		}
		$this->resolve('rejected');
	}

	/**
	 * @return array
	 */
	public function getApiInfo() {
		$result = $this->getAttributes([
			'id',
			'reference_id',
			'reference_type',
			'change_model_id',
			'change_model_type',
			'change_type',
			'resolved_resolution'
		]);
		$result['diff'] = $this->diff;
		$result['reference'] = ($this->reference === null ? null : $this->reference->apiInfo);

		return $result;
	}
}
