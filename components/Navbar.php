<?php 
$ManageUser = $SCADA->checkRolesDisplay();
?>
<section class="navigation">
    <div class="nav-container">
        <div class="brand">
            <a href="./Index.php">
            <img src="../img/PTT.png" width="35" height="45" style="margin-top:-18px;margin-left:-10px;margin-right:3px;"/>
            <b><i>Historical Operation Data Web Application</i></b>
            </a>
        </div>
        <nav>
        
            <div class="nav-mobile">
                <a id="nav-toggle" href="#!"><span></span></a>
            </div>
            <ul class="nav-list">
                <?php if($ManageUser !== 'error' && 
                isset($ManageUser['GROUP_ID']) && $ManageUser['GROUP_ID'] !== '1' 
                && $ManageUser['GROUP_ID'] !== '5' &&
                $ManageUser['GROUP_ID'] !== '3') { ?>
                <li class="dropdown-scada">
                    <a href="#!"><i class="fas fa-user-friends"></i>&nbsp;&nbsp;MANAGE USER</a>
                    <ul class="nav-dropdown-scada">
                      <?php if (isset($ManageUser['GROUP_ID']) && $ManageUser['GROUP_ID'] === '2'){?>
                        <li>
                            <a href="./ManageUser.php?GroupUsesr=1&&GroupName=PTT User">
                                PTT User</a>
                        </li>
                        <?php }?>
                        <?php if (isset($ManageUser['GROUP_ID']) && $ManageUser['GROUP_ID'] === '2'){?>
                        <li>
                            <a href="./ManageUser.php?GroupUsesr=2&&GroupName=PTT Admin">
                                PTT Admin</a>
                        </li>
                        <?php }?>
                        <?php if (isset($ManageUser['GROUP_ID']) && $ManageUser['GROUP_ID'] === '6'){?>
                        <li>
                            <a href="./ManageUser.php?GroupUsesr=5&&GroupName=GMDR User">
                                GMDR User</a>
                        </li>
                        <?php }?>
                        <?php if (isset($ManageUser['GROUP_ID']) && $ManageUser['GROUP_ID'] === '6'){?>
                        <li>
                            <a href="./ManageUser.php?GroupUsesr=6&&GroupName=GMDR Admin">
                                GMDR Admin</a>
                        </li>
                        <?php }?>
                        <?php if (isset($ManageUser['GROUP_ID']) && $ManageUser['GROUP_ID'] === '4'){?>
                        <li>
                            <a href="./ManageUser.php?GroupUsesr=3&&GroupName=SCADA History User"
                                >
                                SCADA History User</a>
                        </li>
                        <?php }?>
                        <?php if (isset($ManageUser['GROUP_ID']) && $ManageUser['GROUP_ID'] === '4'){?>
                        <li>
                            <a href="./ManageUser.php?GroupUsesr=4&&GroupName=SCADA History Admin"
                                >
                                SCADA History Admin</a>
                        </li>
                        <?php }?>
                    </ul>
                </li>
                <?php }?>
                <li class="dropdown-scada">
                    <a href="#!"><i class="fas fa-satellite"></i>&nbsp;&nbsp;SCADA</a>
                    <ul class="nav-dropdown-scada">
                        <li>
                            <a href="#!" onclick="OpenModalScada('1 Minute')">1 Minute</a>
                        </li>
                        <li>
                            <a href="#!" onclick="OpenModalScada('10 Minute')">10 Minute</a>
                        </li>
                        <li>
                            <a href="#!" onclick="OpenModalScada('Hour')">Hour</a>
                        </li>
                        <li>
                            <a href="#!" onclick="OpenModalScada('Day')">Day</a>
                        </li>
                        <?php 
                        if ($ManageUser !== 'error' && 
                          isset($ManageUser['GROUP_ID']) && $ManageUser['GROUP_ID'] !== '1' 
                          && $ManageUser['GROUP_ID'] !== '6'
                          && $ManageUser['GROUP_ID'] !== '5' &&
                          $ManageUser['GROUP_ID'] !== '3') { 
                        ?>
                            <li>
                                <a href="./Template.php">Template Management</a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
                <li class="dropdown-gmdr">
                    <a href="#!"><i class="fas fa-satellite-dish"></i>&nbsp;&nbsp;GMDR</a>
                    <ul class="nav-dropdown-gmdr">
                        <li>
                            <a href="#!" onclick="OpenModalGMDR('Hour')">Hourly</a>
                        </li>
                        <li>
                            <a href="#!" onclick="OpenModalGMDR('Day')">Daily</a>
                        </li>
                        <?php 
                        if ($ManageUser !== 'error' && 
                          isset($ManageUser['GROUP_ID']) && $ManageUser['GROUP_ID'] !== '1' 
                          && $ManageUser['GROUP_ID'] !== '4'
                          && $ManageUser['GROUP_ID'] !== '5' &&
                          $ManageUser['GROUP_ID'] !== '3') { ?>
                        <li>
                            <a href="./MeterConfig.php">Meter Config</a>
                        </li>
                        <?php } ?>
                        <?php 
                        if ($ManageUser !== 'error' && 
                          isset($ManageUser['GROUP_ID']) && $ManageUser['GROUP_ID'] !== '1' 
                          && $ManageUser['GROUP_ID'] !== '4'
                          && $ManageUser['GROUP_ID'] !== '5' &&
                          $ManageUser['GROUP_ID'] !== '3') { ?>
                        <li>
                            <a href="#!" onclick="OpenModalTagConfig()">Tag Config</a>
                        </li>
                        <?php } ?>
                    </ul>
                </li>
                <li class="list-item">
                <?php if($_SESSION['USER'] && strlen($_SESSION['USER']) > 6){ ?>
                  <img src="../img/avatar.jpg" class="avatar">
                <?php } else { ?>
                  <img src="https://hq-web-s13.pttplc.com/directory/photo/<?php echo htmlspecialchars($_SESSION['USER'], ENT_QUOTES, 'UTF-8'); ?>.jpg" class="avatar">
                <?php } ?>
                  <ul class="dropdown-menu-logout">
                    <li><a href="../destroy.php"><i class="fas fa-sign-out-alt"></i>&nbsp;&nbsp;Logout</a></li>
                  </ul>
                </li>
            </ul>
        </nav>
    </div>
</section>