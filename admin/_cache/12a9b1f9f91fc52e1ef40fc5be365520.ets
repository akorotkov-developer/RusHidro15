ETSa:2:{s:7:"0reduce";i:2;s:4:"main";a:19:{s:5:"2:499";s:473:"

    <h1>Настройки защиты</h1>

    <form method="post" name="settings">
        <input type="hidden" name="settings" value="1" />

        <table class="settable">
            <tr>
                <td>
                    Блокировать IP пользователя при неудачных попытках входа:
                </td>
                <td>
                    <input type="checkbox" name="auth_lock" id="auth_lock" value="1" ";s:18:"64:516:auth_lock:1";a:1:{s:5:"2:523";s:7:"checked";}s:5:"2:908";s:382:" />
                    <label for="auth_lock">Да</label>
                </td>
            </tr>
            <tr>
                <td>
                    Уведомлять на e-mail о неудачных попытках входа:
                </td>
                <td>
                    <input type="checkbox" name="auth_email_send" id="auth_email_send" value="1" ";s:24:"64:931:auth_email_send:1";a:1:{s:5:"2:938";s:7:"checked";}s:6:"2:1382";s:441:" />
                    <label for="auth_email_send">Да</label>
                </td>
            </tr>
            <tr>
                <td>
                    E-mail для уведомлений о неудачных попытках входа<br />(несколько e-mail указываются через запятую):
                </td>
                <td>
                    <input type="text" name="auth_email" value="";s:18:"4:1393:auth_email:";s:0:"";s:6:"2:1724";s:330:"" class="input_large" /><br />

                </td>
            </tr>
            <tr>
                <td>
                    Количество возможных неудачных попыток входа:
                </td>
                <td>
                    <select name="auth_count">
                        ";s:20:"16:1742:auth_counts:";a:7:{s:6:"2:1782";s:40:"
                        <option value="";s:13:"4:1788:value:";s:0:"";s:6:"2:1791";s:2:"" ";s:17:"32:1805:selected:";a:1:{s:6:"2:1813";s:8:"selected";}s:6:"2:1817";s:1:">";s:13:"4:1823:value:";s:0:"";s:6:"2:1858";s:34:"</option>
                        ";}s:6:"2:2142";s:277:"
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    Период блокировки:
                </td>
                <td>
                    <input type="text" name="auth_lock_period" value="";s:24:"4:2159:auth_lock_period:";s:0:"";s:6:"2:2616";s:456:"" class="input_short" />
                    минут
                </td>
            </tr>
            <tr>
                <td>
                    Никогда не блокировать доступ в систему администрирования для IP<br />(несколько ip указываются через запятую):
                </td>
                <td>
                    <input type="text" name="auth_ip" value="";s:15:"4:2624:auth_ip:";s:0:"";s:6:"2:3054";s:429:"" class="input_large" /><br />
                </td>
            </tr>
            <tr>
                <td>
                    Всегда блокировать доступ в систему администрирования для IP<br />(несколько ip указываются через запятую):
                </td>
                <td>
                    <input type="text" name="auth_ip_lock" value="";s:20:"4:3067:auth_ip_lock:";s:0:"";s:6:"2:3463";s:395:"" class="input_large" /><br />
                </td>
            </tr>
            <tr>
                <td>
                    Всегда блокировать доступ к сайту для IP<br />(несколько ip указываются через запятую):
                </td>
                <td>
                    <input type="text" name="auth_ip_lock_site" value="";s:25:"4:3481:auth_ip_lock_site:";s:0:"";s:6:"2:3809";s:327:"" class="input_large" /><br />
                </td>
            </tr>
            <tr>
                <td>
                </td>
                <td>
                    <input type="submit" value="Сохранить" class="button_large" />
                </td>
            </tr>
        </table>
    </form>
    
    
    ";s:19:"32:3825:locked_ips:";a:3:{s:6:"2:3946";s:121:"
        <h3>Временно заблокированные ip</h3>
        <table class="locked-ip-table">
            ";s:19:"16:3963:locked_ips:";a:7:{s:6:"2:4001";s:38:"
            <tr>
                <td>";s:10:"4:4004:ip:";s:0:"";s:6:"2:4041";s:36:"</td>
                <td>(<a href="";s:12:"16:4052.1://";a:1:{s:16:"4:4052:page_url:";s:0:"";}s:6:"2:4064";s:11:"?unlock_ip=";s:10:"4:4067:ip:";s:0:"";s:6:"2:4139";s:71:"">разблокировать</a>)</td>
            </tr>
            ";}s:6:"2:4168";s:22:"
        </table>
    ";}s:6:"2:4174";s:3:"


";}}