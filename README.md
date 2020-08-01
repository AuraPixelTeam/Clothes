# Clothes
[![](https://poggit.pmmp.io/shield.state/Clothes)](https://poggit.pmmp.io/p/Clothes)
[![](https://poggit.pmmp.io/shield.dl.total/Clothes)](https://poggit.pmmp.io/p/Clothes)
[![Discord](https://img.shields.io/badge/chat-on+discord-7289da.svg)](https://discord.gg/5CpFadd)
[![HitCount](http://hits.dwyl.com/tungstenvn/clothes.svg)](http://hits.dwyl.com/tungstenvn/clothes)

![GIF](https://github.com/TungstenVn/TungstenVn_poggit_news/blob/master/ezgif-5-0ce7417bfc97.gif)

### Make your skin more splendid
+ **Why would you need to use this plugin:**
  - This plugin will help your server more funny cuz player have something to show up, a wing, a caption american shield and so on.
  - The clothes also has permission for using,so that you can add the custom clothes to specific rank   
+ **Note of the plugin:**
  - GD2 extension required, do as following construction on the console's message (in case you dont know what it is)
  - FormAPI lib required, download this plugin (Clothes) from poggit (which will be .phar type) if you dont similar to this
  - That's all
## All Features
 - [✅] Add some extra geometry into player skin (/clo)
 - [✅] Change entire player skin into new one (/cos)
 - [✅] Change a human entity skin intro new one(/nanny)
 - [✅] Update checker
 - [❌] Support Persona Skin
   - Who use persona skin will be replace to a unicorn skin if using clothes
## About clothes/cosplay
### How to add more clothes/cosplays into form
 + **Example:** You want to create a button named **Hat** in ``/clo`` form.When you click the button, you want to have a cloth named **Cowboy**:
   - You go to plugins_data/clothes create a folder named **Hat**
   - Go inside **Hat** folder, put 2 files, one named **Cowboy.json**, one named **Cowboy.png**
   - Then it should be works
 + **Note**:
   - The clothes must inside the **plugins_data/clothes** folder.
   - The .json and .png must inside the **plugins_data/clothes/``AnotherFolderName``** folder,in this case, **plugins_data/clothes/Hat**
   - Do the same for Cosplays feature.
### Where to get clothes/cosplays
 - You need to draw it, i'm using blockbench.net
 + **For Clothes**:
   - First of all,youtube how to use blockbench
   - If you are new ,just importing some random clothes (.json file) inside the plugin datas folder to the blockbench
   - Now you can see a steve geometry with some extra cube(geometry), you can delete that cube or add more
     - but DO NOT touch the steve geometry
   - Move to the texture, add the example texture to see how i drawed it, you need to find a unuse spot
     - on the texture, draw on it, maybe in the neck, empty spot,etc.
	 - texture can be in 64x64 or 128x128
	 - (And the way i do it is that I import a steve skin, than draw the extra texture in empty spot,than delete the steve piece after finished)
   - Then save the json and texture file with same name.
   - After you have the json file, go into it and change the geometry's name to **geometry.``abc``/``xyz``**
     - **abc** is **clothes** or **cosplays**. **xyz** is the folder's name which is contained the clothes/cosplays
	 - if you do the tutorial at [About clothes/cosplay](#about-clothescosplay), the geometry's name should be **geometry.clothes/Hat**
   - And also you have the .png file, go to some applications (3d paint on win10,..) which have eraser tool to remove pixels that the clothes dont use
 + **For Cosplays**:
   - You should add a steve geometry json file then draw on it so the body,arm,leg can moving while player is moving 
   - Draw the texture to fit with the geometry
   - Then add json, png into plugin_datas just like clothes (but in cosplays folder).
   - How the cosplay look like in blockbench should be just how it will displayed on the server.
 + If you having some problem just leave a comment under the poggit review or open a issue on github. 
## Commands
 + Type **/clo** or **/clothes** and a UI will display for you to chosing the clothes
 + Type **/cos** or **/cosplays** to change the skin
   - both /clo and /cos dont have permission to use, but there is permission for each cloth/cosplay (if you have set in config)
 + **/nanny** 
   - only who has permission **nanny.clothes.command** can use this command
   - use this command then hitting a human entity to change its skin 
## Config.yml
 + Type the clothes' name into the config like example to assign it with a permission for using
 + If you want to remove perm, just remove something similar to : ``long_neck: tungdeptrai.perm``.
 + Config looks like:
```yaml
---
#set to true if you wanna delete the player's skin in saveskin dir when the player left the server
DeleteSkinAfterQuitting: false

#get update for new release
enableUpdateChecker: true

#type as the following to add a perm to a cloth or cosplay
perms:
  pika blue: pika.perm
  sitdown: sitdown.perm

  long_neck: tungdeptrai.perm
```
## Introduce
 + A video about the plugin:
   
   [![Youtube Introduce](https://img.youtube.com/vi/ZGMaG80Wi3g/0.jpg)](https://www.youtube.com/watch?v=ZGMaG80Wi3g)
## More Clothes
- You can go here to get some more clothes without drawing skill
  - https://github.com/TungstenVn/Clothes-Addon
<a align=**center**><img src=**https://i.ibb.co/K7pdzTS/Screenshot-10.png**></a>
