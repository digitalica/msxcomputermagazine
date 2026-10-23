(* Twisting bars the precalc by
   An-ti Silvast 2000
   For use on Borland Pascal 1.0
   for the MSX-series.

   Takes some 5 minutes to calc
   through on my 3.5 MHz MSX1.
   Never mind, it's a great dynamic
   break, go pick a book or
   do something else truly creative..

   Sorry for virtually no comments.
   Write to asilvast@cc.hut.fi
   *)

const texture:array[0..639]
of byte=(
0,0,0,0,0,0,0,1,0,0,
0,1,0,0,0,0,0,0,1,0,
0,0,1,1,1,0,0,0,0,1,
1,1,1,0,1,0,0,0,0,1,
1,1,1,0,0,0,0,1,1,1,
0,0,0,0,0,0,1,1,1,1,
1,0,0,0,0,0,1,0,1,1,
0,0,0,1,0,1,1,0,0,0,
1,0,0,0,0,0,0,0,0,0,
1,1,1,0,0,0,1,1,1,1,
0,0,0,1,0,0,0,0,1,1,
1,0,1,1,0,0,0,0,0,1,
0,1,1,0,0,0,1,0,1,1,
0,0,0,0,0,0,0,1,0,1,
1,1,0,0,0,0,1,1,1,0,
0,0,1,1,1,1,0,0,0,0,
1,1,1,1,1,1,0,0,0,0,
1,0,1,1,0,0,0,1,0,1,
1,0,0,0,0,0,0,0,1,0,
1,1,1,0,0,0,0,0,0,1,
1,1,0,0,0,1,1,1,1,0,
0,0,1,0,0,0,0,1,1,1,
0,1,1,1,0,0,0,1,0,0,
0,0,0,0,1,0,0,0,0,0,
1,1,1,1,1,0,0,0,0,0,
1,1,1,1,0,0,0,0,1,1,
1,0,0,0,0,0,0,1,1,1,
1,1,0,0,0,0,1,0,0,0,
1,0,0,0,0,0,0,1,0,0,
0,1,1,1,0,0,0,0,1,1,
1,1,0,1,0,0,0,0,0,0,
0,0,0,0,0,0,0,0,0,0,
0,0,1,1,1,1,0,0,0,0,
0,0,1,1,0,0,0,0,1,0,
0,0,0,0,0,0,0,0,0,1,
1,0,0,0,1,0,0,0,0,0,
0,1,0,0,0,1,1,1,0,0,
0,0,1,1,1,1,0,1,0,0,
0,0,0,0,0,0,0,0,0,0,
0,0,0,0,0,0,0,0,0,0,
0,0,0,1,1,1,1,1,0,0,
0,0,0,0,0,0,0,0,0,0,
0,0,1,1,1,0,1,0,0,0,
0,0,0,1,1,1,0,0,0,1,
1,1,1,0,0,0,1,0,0,0,
0,1,1,1,0,1,1,0,0,0,
0,0,0,0,0,0,0,1,0,1,
1,1,1,0,0,0,0,0,0,0,
0,0,0,0,0,1,1,0,0,0,
0,0,0,0,0,0,0,0,0,0,
1,1,1,1,1,1,1,1,1,0,
0,0,0,1,0,1,1,0,0,0,
1,0,1,1,0,0,0,0,0,0,
0,1,0,1,1,1,0,0,0,0,
0,0,0,0,0,0,1,1,1,1,
0,1,1,1,0,0,0,0,0,0,
0,0,0,0,0,0,0,0,0,0,
0,0,0,0,0,0,0,0,0,0,
0,0,0,0,0,0,0,0,0,0,
0,0,0,0,0,0,0,0,0,0,
0,0,0,0,0,0,0,0,0,0,
0,0,0,0,0,0,0,0,0,0,
0,0,0,0,0,0,0,0,0,0,
0,0,0,0,0,0,0,0,0,0
);

var buffer:array[0..10239]
           of byte;
    temp:array[0..79] of byte;

    pre_calc:array[0..191] of integer;

const powers_of_2:Array[0..7]
        of integer
  =(1,2,4,8,16,32,64,128);

function lin_to_byte(p:integer;
  c1,c2:byte):byte;
var w:integer;
    sum:byte;
begin

 sum:=0;
 for w:=0 to 7 do
   if (temp[p+w]<>c1) then
     sum:=sum+powers_of_2[7-w];

 lin_to_byte:=sum;
end;


procedure viiva(y,x1,x2,
                u1,u2,freme:Integer);
var i,x,du,t:integer;
    uu:integer;
begin
  if (x2<x1) then begin
    t:=x2;
    x2:=x1;
    x1:=t;
  end;

  du:=(u2-u1)*256 div (x2-x1);
  uu:=u1*256;

  for x:=x1 to x2 do begin
     temp[x-120]:=
       texture[uu shr 8];
     uu:=uu+du;
  end;

end;
var k,x,xx,y,yy,dx:integer;
    kkk:integer;
    pohjat:integer;
    x1,y1,z1:integer;
    x2,y2,z2:integer;
    x3,y3,z3:integer;
    x4,y4,z4:integer;
    xs1,xs2,xs3,xs4:integer;
    f:file of byte;
    b:byte;
begin

 for k:=0 to 63 do begin

   for kkk:=0 to 7 do begin

   fillchar(temp,sizeof(temp),0);
   x1:=round(cos(k*2*pi/128)*56);
   z1:=round(sin(k*2*pi/128)*64+192);
   x2:=round(cos((k+32)*2*pi/128)*56);
   z2:=round(sin((k+32)*2*pi/128)*64+192);
   x3:=round(cos((k+64)*2*pi/128)*56);
   z3:=round(sin((k+64)*2*pi/128)*64+192);
   x4:=round(cos((k+96)*2*pi/128)*56);
   z4:=round(sin((k+96)*2*pi/128)*64+192);

   xs1:=((x1)*128 div z1)+160;
   xs2:=((x2)*128 div z2)+160;
   xs3:=((x3)*128 div z3)+160;
   xs4:=((x4)*128 div z4)+160;

   pohjat:=k*80+kkk*10;
   if (k<22*2) or (k>58*2) then
       viiva(kkk,xs3,xs4,
         kkk*80+0,kkk*80+64,pohjat);

   if (k<6*2) or (k>42*2) then
       viiva(kkk,xs4,xs1,
         kkk*80+0,kkk*80+64,pohjat);

   if (k>10*2) and (k<38*2) then
       viiva(kkk,xs2,xs3,
         kkk*80+0,kkk*80+64,pohjat);

   if (k>27*2-1) and (k<54*2) then
     viiva(kkk,xs1,xs2,
       kkk*80+0,kkk*80+64,pohjat);

   for x:=0 to 9 do
     buffer[k*80+kkk*10+x]:=
     lin_to_byte(x*8,0,1);

   end;
   writeln('frame ',k,' of 64.');
 end;

 for y:=0 to 191 do
   pre_calc[y]:=
    (y shr 1 and 63)*80+
    (y and 7)*10;


 move(buffer,buffer[64*80],64*80);

 assign(f,'twisted.dat');
 rewrite(f);

 for y:=0 to 191
   do begin
     b:=pre_calc[y] and $FF;
     write(f,b);
     b:=pre_calc[y] shr 8;
     write(f,b);
   end;

 for y:=0 to 10239 do
   write(f,buffer[y]);

 close(f);
end.
