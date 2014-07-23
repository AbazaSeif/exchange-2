<h1>Часто задаваемые вопросы</h1>
<div id="faq-wrapper">
    <ol class="faq-main">
        <li><span class="label">Как определяется ставка-победитель</span>
            <div class="paragraph">
                Победителем аукциона считается ставка, в которой указана наименьшая цена. При наличии нескольких ставок с одинаковой ценой победителем считается ставка сделанная раньше (время проставляется в каждой ставке, часовой пояс - московское время).
            </div>
        </li>
        <li><span class="label">Как активировать/блокировать пользователя</span>
            <div class="paragraph">
                <div>Открыть "Перевозчики->Компании" или "Перевозчики->Контактные лица", найти нужного перевозчика (можно поиском), изменить поле "Статус", нажать кнопку "Сохранить". О смене статуса пользователь получит уведомление по почте.</div>
                <div>У пользователя может быть 5 статусов:</div>
                <div>
                    <ol>
                        <li>Не подтвержден <span class="blocked">(ограничение доступа)</span> - пользователь подал заявку, но она еще не была рассмотрена.</li>
                        <li>Активен <span class="no-blocked">(без ограничения доступа)</span> - пользователь допускается до участия в торгах, о смене статуса он получит уведомление по почте.</li>
                        <li>Предупрежден <span class="no-blocked">(без ограничения доступа)</span> - предупреждение должно быть описано в поле "Причина". Пользователь получит уведомление по почте, в котором будет содержаться повод для смены статуса.</li>
                        <li>Заблокирован <span class="blocked">(ограничение доступа)</span> - блокировка доступа будет наложена на неопределенный срок, основание для смены статуса должно быть описано в поле "Причина". Пользователь получит уведомление по почте, в котором будет содержаться повод для смены статуса. <div>В данном случае <strong>блокировка снимается вручную</strong>. При снятии блокировки пользователь получает письмо по почте.</div></li>
                        <li>Временно заблокирован <span class="blocked">(ограничение доступа на указанный срок)</span> - блокировка доступа будет наложена до указанной даты, основание для смены статуса должно быть описано в поле "Причина". В данном случае <strong>блокировка снимается автоматически</strong> при наступлении указанной даты. При снятии блокировки пользователь получает письмо по почте.
                            <div class="paragraph">
                                До какой даты будет временная блокировка можно указать двумя способами:
                                <div>а) Расширенный режим (cтраница редактирования пользователя)</div><img src="<?php echo Yii::app()->getBaseUrl(true) ?>/images/faq/faq-status-temp-block-1.jpg" height="150">
                                <div>б) Сокращенный режим (страница со списком пользователей) </div><img src="<?php echo Yii::app()->getBaseUrl(true) ?>/images/faq/faq-status-temp-block-2.jpg"  height="150">
                            </div>
                        </li>
                    </ol>
                </div>
            </div>
        </li>
        
        <li><span class="label">Как указывать время в перевозках</span>
            <div class="paragraph">
                Часовой пояс - московское время, поэтому если требуется чтобы перевозка была закрыта в 13:00 по Белорусскому времени, пишем 14:00 по Московскому времени.
            </div>
        </li>
        <li><span class="label">Если пользователь незалогинен и видит, что на бирже есть перевозки, но войдя в свой профиль(аккаунт) ему выдается сообщение "Нет перевозок"</span>
            <div class="paragraph">
                <div>Если пользователь незалогинен и видит, что на бирже есть перевозки, но войдя в свой профиль(аккаунт) ему выдается сообщение "Нет перевозок", то он должен зайти во вкладку "Настройки", раздел "Параметры отображения", проверить какой пункт у него выбран:</div>
                <div>- "Отображать только региональные заявки на перевозку"</div>
                <div>- "Отображать только международные заявки на перевозку"</div>
                <div>- "Отображать все заявки на перевозку"</div>
                <div>при необходимости выбрать другой пункт и нажать кнопку "Сохранить".</div>
            </div>
        </li>
        <li><span class="label">Как сделать принтскрин (PrintScreen)</span>
            <div class="paragraph">
                <div>На клавиатуре нажать кнопку, на которой написано PrintScreen или PrtSc.
                После нажатия на эту кнопку внешне ничего не происходит, никаких новых окон не открывается, снимок помещается в оперативную память. 
                Чтобы сохранить скриншот на диск нужно вставить содержимое буфера обмена в любую программу, способную обрабатывать графику. Вполне подойдут Word, Paint, Photoshop, Skype. 
                Например, в Skype нужно курсор поставить в окно для создания сообщения и удерживая кнопку Ctrl нажать V (Ctrl+V).
                </div>
                <strong>Нюансы создания снимков экрана на ноутбуке и нетбуке:</strong>
                <div>
                Для того, чтобы сделать скриншот на ноутбуке/нетбуке, нужно найти кнопку Print Screen (PrtSc) и убедиться, что ее цвет такой же, как и у остальных буквенных и цифровых клавиш.
                Если цвет отличается, совпадая с цветом кнопки Fn или просто не получается просто нажать PrtSc, то нужно нажимать клавишу PrtScn обязательно удерживая при этом клавишу Fn (Fn+PrtScn).
                </div>
            </div>
        </li>
    </ol>
    <div class="faq-section">Восстановение и смена пароля</div>
    <ol class="faq-main">
        <li><span class="label">Пользователь помнит пароль, но хочет его поменять</span>
            <div class="paragraph">
                Если пользователь помнит свой пароль, но хочет его поменять, то он должен зайти в свой профиль(аккаунт), вкладка "Настройки", раздел "Изменить пароль", ввести текущий пароль, новый пароль 2 раза и нажать кнопку "Сохранить".
            </div>
        </li>
        <li><span class="label">Пользователь пытался сам поменять пароль</span>
            <div class="paragraph">
                Пользователь пытался поменять пароль через функцию "Восстановление доступа", но ему выдало сообщение "В вашей учетной записи отсутствует email, поэтому Вы не можете восстановить пароль. Вам требуется связаться с логистами и попросить их внести в вашу учетную запись email.".
                Открываем в админке редактирование этого пользователя ("Перевозчики->Компании" или "Перевозчики->Контактные лица") и проверяем поле "Email" (в нем нет значения). Следует спросить у пользователя email (он должен отличаться от почтовых ящиков контактных лиц), внести в поле "Email" и нажать кнопку "Сохранить".
                Далее следует сказать пользователю еще раз воспользоваться функцией "Восстановление доступа".
            </div>
        </li>
        <li><span class="label">Пользователь забыл пароль</span>
            <div class="paragraph">
                <div>Прежде, чем предлагать пользователю указанные ниже способы убедитесь, что он не имеет статус "Блокирован", "Временно блокирован" или "Не подтвержден" - это статусы с ограничением доступа, смена пароля не даст возможность входа в профиль.</div>
                <div>Общее правило: <strong>компания</strong> в строке "Логин, ИНН, email" может указывать <strong>ИНН или email</strong>, а <strong>контактное лицо</strong> - <strong>email</strong>.</div>
                <div><strong>Способ №1</strong></div>
                <div class="paragraph">
                    <div>Предлагаем пользователю воспользоваться функцией "Восстановление доступа" (http://exchange.lbr.ru/site/restore), которая вышлет ему на почту новый пароль.</div>
                    <img src="/images/faq/faq-restore.jpg" height="200">
                </div>
                <div><strong>Способ №2</strong></div>
                <div class="paragraph">
                    <div>Если после первого способа пользователь утверждает, что не может зайти, открываем в админке редактирование этого пользователя ("Перевозчики->Компании" или "Перевозчики->Контактные лица") в поле "Пароль" вводим пароль (должен содержать буквы и цифры), кнопка "Сохранить". Даем пользователю этот пароль и говорим, чтобы после входа в свой профиль он его сменил. Для этого он должен зайти во вкладку "Настройки", раздел "Изменить пароль".</div>
                </div>
                <div><strong>Способ №3</strong></div>
                <div class="paragraph">
                    <div>Если после второго способа пользователь говорит, что все еще не может зайти выполняем способ №2 еще раз, и до того, как отдать пароль пользователю проверяем, т.е. вводим его пароль, пробуем зайти. И только после проверки отдаем ему и напоминаем, чтобы не забыл поменять пароль.</div>
                </div>
            </div>
        </li>
        
        <!--li><span class="label">Как создать перевозку</span>
            <div class="paragraph">
                <div>Создать перевозку можно из 1С и из самой биржи. При создании следует внимательно читать и заполнять поля, обратите особое внимание на поле <strong>"Тип перевозки"</strong>.</div>
                <div>Независимо от способа создания существует список полей обязательных для заполнения.</div>
            </div>
        </li-->
    </ol>
</div>
