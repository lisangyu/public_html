#!/bin/bash

# EMBOSS工具执行序列比对和motif扫描
input_sequence="$1"  # 输入蛋白质序列文件
output_file="$2"     # 输出结果文件

# 运行EMBOSS进行比对或motif扫描
needle -asequence $input_sequence -bsequence $input_sequence -gapopen 10.0 -gapextend 0.5 -outfile $output_file
# 也可以用类似的命令进行motif扫描
# patmatmotifs -sequence $input_sequence -outfile $output_file

echo "EMBOSS分析已完成，结果保存在 $output_file"
