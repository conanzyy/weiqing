<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<script>document.title = "我的中奖记录"; </script>

<link rel="stylesheet" href="../addons/ewei_shopv2/plugin/lottery/static/style/myreward.css?<?php  echo time();?>" />

<div class='fui-page  fui-page-current'>

    <div class="fui-content">

        <div class="lottery-head">
            <img class="member_head" src="<?php  echo $member['avatar'];?>" >
            <p><?php  echo $member['nickname'];?></p>
        </div>
        <div class="lottery-title"><span class="title-left">我的中奖记录</span></div>

        <div class="lottery-content">

            <?php  if(!empty($mylog)) { ?>
            <div class="fui-list-group" id="container" style="margin-top: 0;"></div>
            <!--<div class='infinite-loading'><span class='fui-preloader'></span><span class='text'> 正在加载...</span></div>-->
            <script id='tpl_shop_notice' type='text/html'>
                <%each list as log%>
                <div class="reward_item">
                    <div class="reward_icon">
                        <img src="<%log.icon%>" >
                    </div>
                    <div class="reward_content">
                        <p ><%log.title%></p>
                        <p style="color: #a8a8a8;">时间:<%log.addtime%></p>
                    </div>
                    <div class="item-btn"><a  <%if log.rewarded==0%>href="<%log.link%>"<%/if%> class="btn <%if log.rewarded==1%>  btn-default-o <%else%> btn-danger <%/if%> btn-xs" data-nocache="true"><%if log.rewarded==1%><span class="icon icon-check"></span>已领取<%else%>去领取<%/if%></a></div>
                </div>
                <%/each%>
            </script>
            <script language='javascript'>require(['../addons/ewei_shopv2/plugin/lottery/static/js/myreward.js'], function (modal) {modal.init();});</script>
            <?php  } else { ?>
            <p style="text-align: center;">暂时没有奖励..</p>
            <?php  } ?>

        </div>

    </div>

</div>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>


