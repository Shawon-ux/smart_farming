<div class="bd-testi">
           <div class="container">
             <div class="bd-grid">
             <?php
             $limit = $limit ?? 6;
             $T = [
              ["t1","রহিম উদ্দিন","Rice farmer, Mymensingh","Boro season-এ cost calculator দিয়ে সার আর কীটনাশকের খরচ আগেই হিসাব করতে পেরেছি। এবার বাড়তি খরচ ছাড়াই ভালো ফলন পেয়েছি।"],
              ["t2","Fatema Begum","Paddy grower, Rajshahi","The chatbot answered my questions about pests in simple Bangla and told me which fertilizer suits my loamy soil. Very helpful during harvest."],
              ["t3","করিম মিয়া","Aman farmer, Comilla","Aman রোপণের সময় helper খুঁজে পাওয়া কঠিন ছিল। Projects পেজে সাইন-আপ করে কয়েকজন কর্মী পেয়ে গেছি।"],
              ["t4","Sultana Akter","Vegetable farmer, Bogra","I submitted my vegetable project to the admin and it was approved in a day. Now neighbours from nearby villages join as helpers."],
              ["t5","আব্দুল হালিম","Jute farmer, Jessore","কাছের বাজারের নাম আর দাম এক জায়গায় পেয়ে যাই। কোথায় ফসল বিক্রি করলে লাভ বেশি, সেটা বুঝতে সুবিধা হয়।"],
              ["t6","Nasima Khatun","Smallholder, Rangpur","Renting a power tiller through the equipment list saved me a lot of money. The platform is simple even for first-time users."],
             ];
             foreach (array_slice($T, 0, $limit) as [$img,$name,$role,$text]): ?>
               <div class="bd-card">
                 <div class="bd-photo" style="background-image:url(images/bd/<?= $img ?>.jpg)"></div>
                 <div class="bd-body">
                   <div class="bd-stars">★★★★★</div>
                   <p>“<?= htmlspecialchars($text) ?>”</p>
                   <div class="bd-who"><span class="bd-av"><?= mb_substr($name,0,1) ?></span><div><strong><?= htmlspecialchars($name) ?></strong><small><?= htmlspecialchars($role) ?></small></div></div>
                 </div>
               </div>
             <?php endforeach; ?>
             </div>
           </div>
         </div>
