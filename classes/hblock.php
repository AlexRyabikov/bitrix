<?
//Классы для работы с ХЛ-блоками
use Bitrix\Main\Loader; 
use Bitrix\Highloadblock as HL; 
use Bitrix\Main\Entity;

//Для логирования обращение к внешнему API
class ServiceApiLog
{
	private static function Connection()
	{
		$otvet = array();
		Loader::includeModule("highloadblock"); 
		$otvet["hlblock"] = HL\HighloadBlockTable::getById(HLBLOCK_SERVICE_API_LOG)->fetch(); 
		$otvet["entity"] = HL\HighloadBlockTable::compileEntity($otvet["hlblock"]); 
		$otvet["entity_data_class"] = $otvet["entity"]->getDataClass(); 
		return $otvet;
	}	
	public static function Record($method_name,$user_id="")
	{
		$connect = self:: Connection();
		//Если пользователь не указан то текущий
		if(!$user_id)
		{
			global $USER;
			$user_id = $USER->GetId();
		}
		//Добавляем
		$arPole = array(
            "UF_SAPI_USERID"=>$user_id,
            "UF_SAPI_METHOD"=>$method_name,
            "UF_SAPI_TIMESTAMP"=>date("d.m.Y H:i:s")
		);		
		$otvet = $connect["entity_data_class"]::add($arPole);	
	}
}

//Пример вызова:
//GooogleApiLog::Record("GetData");

//Класс для расчета ограничений вывода элементов на страницу (с учетом нагрузки на внешний API)
class ServiceLimitsLoadBalancer 
{
	private $ghlblock;
	private $gentity;
	private $gentity_data_class;
	private $garPole = array();
	
	//Дефолтные значения (с заделом на будущее)     
	private const DEFAULT_ACCOUNT_LIMIT = 10;
    private const DEFAULT_LAG = 15;
	//Градация лимитов
	private array $limitsGradation = [
        "2.0" => ['limitAccounts' => 3, 'limitLag' => 30],
        "1.5" => ['limitAccounts' => 5, 'limitLag' => 30],
        "1.0" => ['limitAccounts' => 7, 'limitLag' => 20],
        "0.0" => ['limitAccounts' => 10, 'limitLag' => 15],
    ];
	
	function __construct()
	{
		Loader::includeModule("highloadblock"); 
		$this->ghlblock = HL\HighloadBlockTable::getById(HL_BLOCK_CONSTANT)->fetch(); 
		$this->gentity = HL\HighloadBlockTable::compileEntity($this->ghlblock); 
		$this->gentity_data_class = $this->gentity->getDataClass(); 		
	}
	
	//Если нужно вернуть дефолтные значения
	private function getDefaultLimits()
    {
        return [
            'limitAccounts' => self::DEFAULT_ACCOUNT_LIMIT,
            'limitLag' => self::DEFAULT_LAG,
        ];
    }
	
	//Возвращаем лимиты
    public function getLimitsByUserId($userId='')
    {
        $gdata = $this->gentity_data_class::getList([
            'select' => ['*'],
            'order' => ['ID' => 'DESC'],
            'filter' => ['UF_SAPIB_USR_ID' => $userId]
        ]);

        if ($grow = $gdata->fetch()) {
            $usagePercent = $grow['UF_SAPIB_USAGE_PERC'];
            return $this->determineLimits($usagePercent);
        }

        return $this->getDefaultLimits();
    }

	//Рассчитываем лимиты
    private function determineLimits($usagePercent)
    {
        foreach ($this->limitsGradation as $threshold => $limits) {
            if ($usagePercent >= (float)$threshold) {
                return $limits;
            }
        }
        return $this->getDefaultLimits();
    }
	
}

//Пример вызова:
//global $USER;
//$googleLoad = new GoogleLimitsLoadBalancer();
//$googleBalanceResultArr = $googleLoad->getLimitsByUserId($USER->GetID());